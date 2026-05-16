<?php

namespace App\Http\Controllers;

use App\Models\Artisan;
use App\Models\Category;
use App\Models\Post;

class SitemapController extends Controller
{
    private const LOCALES = ['fr', 'en', 'ar'];

    public function index()
    {
        $artisans   = Artisan::whereNotNull('slug')->get(['slug', 'updated_at']);
        $categories = Category::whereNotNull('slug')->get(['slug', 'updated_at']);
        $posts      = Post::whereNotNull('slug')->where('is_published', true)->get(['slug', 'updated_at']);

        return response()->view('sitemap', [
            'locales'    => self::LOCALES,
            'artisans'   => $artisans,
            'categories' => $categories,
            'posts'      => $posts,
        ])->header('Content-Type', 'text/xml');
    }
}
