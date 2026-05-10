<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Artisan;
use Inertia\Inertia;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = Artisan::query()
            ->with('category')
            ->withAvg('reviews', 'rating'); // Adds reviews_avg_rating

        // 1. Search term — name/bio are JSON; search current locale then fall back to EN
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $locale     = app()->getLocale();
            $query->where(function ($q) use ($searchTerm, $locale) {
                $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.\"{$locale}\"')) LIKE ?", ["%{$searchTerm}%"])
                  ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.en')) LIKE ?", ["%{$searchTerm}%"])
                  ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(bio, '$.\"{$locale}\"')) LIKE ?", ["%{$searchTerm}%"])
                  ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(bio, '$.en')) LIKE ?", ["%{$searchTerm}%"])
                  ->orWhere('city', 'like', "%{$searchTerm}%");
            });
        }

        // 1b. City (used by localSearch SEO route)
        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        // 2. Category ID
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // 3. Minimum average rating
        if ($request->filled('min_rating')) {
            $query->whereRaw('(SELECT COALESCE(AVG(rating), 0) FROM reviews WHERE reviews.artisan_id = artisans.id) >= ?', [$request->min_rating]);
        }

        // 4. Distance (Haversine formula)
        if ($request->filled('lat') && $request->filled('lng') && $request->filled('distance')) {
            $lat = (float) $request->lat;
            $lng = (float) $request->lng;
            $distance = (float) $request->distance;

            $query->selectRaw(
                "*, (6371 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(lng) - radians(?)) + sin(radians(?)) * sin(radians(lat)))) AS distance",
                [$lat, $lng, $lat]
            )->whereRaw(
                "(6371 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(lng) - radians(?)) + sin(radians(?)) * sin(radians(lat)))) <= ?",
                [$lat, $lng, $lat, $distance]
            )->orderBy('distance');
        } else {
            $query->latest();
        }

        // 5. Verified only
        if ($request->boolean('verified')) {
            $query->where('is_verified', true);
        }

        $artisans = $query->paginate(12)->withQueryString();

        return Inertia::render('Search/Index', [
            'artisans' => $artisans,
            'categories' => \App\Models\Category::all(),
            'filters' => $request->only(['search', 'category_id', 'lat', 'lng', 'distance', 'min_rating', 'verified']),
        ]);
    }

    public function localSearch(Request $request, $locale, $service, $city)
    {
        // Find category by slug
        $category = \App\Models\Category::where('slug', $service)->first();
        
        // Merge into request for reuse of index logic
        $request->merge([
            'category_id' => $category?->id,
            'city'        => str_replace('-', ' ', $city),
        ]);

        return $this->index($request);
    }
}
