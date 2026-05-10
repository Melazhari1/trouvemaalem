<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Category;
use App\Models\Artisan;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('artisans')->get();
        $artisans = Artisan::with('category')->get();
        $topArtisans = Artisan::with('category')
            ->orderBy('rating', 'desc')
            ->take(10)
            ->get();

        // Extract unique cities for the filter dropdown
        $cities = Artisan::whereNotNull('city')
            ->distinct()
            ->pluck('city')
            ->sort()
            ->values();

        $topFaqs = \App\Models\Faq::where('is_active', true)
            ->orderBy('order')
            ->take(5)
            ->get();

        return Inertia::render('Home', [
            'categories' => $categories,
            'artisans' => $artisans,
            'topArtisans' => $topArtisans,
            'cities' => $cities,
            'topFaqs' => $topFaqs,
        ]);
    }
}
