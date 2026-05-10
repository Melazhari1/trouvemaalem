<?php

namespace App\Http\Controllers;

use App\Models\Artisan;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $artisans = Artisan::whereNotNull('slug')->get();

        return response()->view('sitemap', [
            'artisans' => $artisans,
        ])->header('Content-Type', 'text/xml');
    }
}
