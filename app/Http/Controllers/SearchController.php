<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Artisan;
use Inertia\Inertia;

class SearchController extends Controller
{
    public function index(Request $request, array $extraProps = [])
    {
        $query = Artisan::query()
            ->with('categories')
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
            $query->whereHas('categories', fn ($q) => $q->where('categories.id', $request->category_id));
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

        return Inertia::render('Search/Index', array_merge([
            'artisans'   => $artisans,
            'categories' => \App\Models\Category::all(),
            'filters'    => $request->only(['search', 'category_id', 'lat', 'lng', 'distance', 'min_rating', 'verified']),
            'schema'     => $this->getGenericSearchSchema($request, $artisans->total()),
        ], $extraProps));
    }

    public function localSearch(Request $request, $_locale, string $service, string $city)
    {
        $category = \App\Models\Category::where('slug', $service)->first();
        $cityName = str_replace('-', ' ', $city);

        $request->merge([
            'category_id' => $category?->id,
            'city'        => $cityName,
        ]);

        return $this->index($request, [
            'schema' => $this->getLocalSearchSchema($category, $cityName),
        ]);
    }

    private function getGenericSearchSchema(Request $request, int $total): array
    {
        $baseUrl = config('app.url');
        $locale  = app()->getLocale();
        $pageUrl = "{$baseUrl}/{$locale}/search";

        return [
            '@context' => 'https://schema.org',
            '@graph'   => [
                [
                    '@type'       => 'SearchResultsPage',
                    '@id'         => "{$pageUrl}#webpage",
                    'url'         => $pageUrl,
                    'name'        => 'Recherche d\'artisans — TrouveMaalem',
                    'description' => "Trouvez des artisans qualifiés au Maroc. {$total} résultats disponibles.",
                    'inLanguage'  => $locale,
                    'isPartOf'    => ['@id' => "{$baseUrl}/#website"],
                    'breadcrumb'  => [
                        '@type'           => 'BreadcrumbList',
                        'itemListElement' => [
                            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Accueil',   'item' => "{$baseUrl}/{$locale}/"],
                            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Recherche', 'item' => $pageUrl],
                        ],
                    ],
                ],
            ],
        ];
    }

    private function getLocalSearchSchema(?\App\Models\Category $category, string $city): array
    {
        $baseUrl  = config('app.url');
        $locale   = app()->getLocale();
        $citySlug = str_replace(' ', '-', mb_strtolower($city));
        $pageUrl  = $category
            ? "{$baseUrl}/{$locale}/{$category->slug}-a-{$citySlug}"
            : "{$baseUrl}/{$locale}/search";

        $serviceName = $category ? "{$category->name} à {$city}" : "Artisans à {$city}";

        $breadcrumbs = [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Accueil', 'item' => "{$baseUrl}/{$locale}/"],
        ];
        $pos = 2;
        if ($category) {
            $breadcrumbs[] = ['@type' => 'ListItem', 'position' => $pos++, 'name' => $category->name, 'item' => "{$baseUrl}/{$locale}/categories/{$category->slug}"];
        }
        $breadcrumbs[] = ['@type' => 'ListItem', 'position' => $pos, 'name' => $serviceName, 'item' => $pageUrl];

        $graph = [
            [
                '@type'       => 'SearchResultsPage',
                '@id'         => "{$pageUrl}#webpage",
                'url'         => $pageUrl,
                'name'        => "{$serviceName} — Professionnels Vérifiés | TrouveMaalem",
                'description' => "Trouvez les meilleurs " . ($category ? mb_strtolower($category->name) : 'artisans') . " à {$city}. Professionnels vérifiés disponibles sur TrouveMaalem.",
                'inLanguage'  => $locale,
                'isPartOf'    => ['@id' => "{$baseUrl}/#website"],
                'about'       => ['@id' => "{$pageUrl}#service"],
                'breadcrumb'  => ['@type' => 'BreadcrumbList', 'itemListElement' => $breadcrumbs],
            ],
        ];

        if ($category) {
            $graph[] = [
                '@type'       => 'Service',
                '@id'         => "{$pageUrl}#service",
                'name'        => $serviceName,
                'description' => "Services de {$category->name} professionnels à {$city} — disponibles et vérifiés sur TrouveMaalem.",
                'provider'    => ['@id' => "{$baseUrl}/#organization"],
                'serviceType' => $category->name,
                'areaServed'  => [
                    '@type'            => 'City',
                    'name'             => $city,
                    'containedInPlace' => [
                        '@type'  => 'Country',
                        'name'   => 'Maroc',
                        'sameAs' => 'https://www.wikidata.org/wiki/Q1028',
                    ],
                ],
            ];
        }

        return ['@context' => 'https://schema.org', '@graph' => $graph];
    }
}
