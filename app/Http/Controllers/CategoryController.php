<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('artisans')->get();
        return Inertia::render('Categories/Index', [
            'categories' => $categories,
        ]);
    }

    public function show($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $artisans = \App\Models\Artisan::where('category_id', $category->id)->with('category')->get();

        return Inertia::render('Categories/Show', [
            'category' => $category,
            'artisans' => $artisans,
        ]);
    }
}
