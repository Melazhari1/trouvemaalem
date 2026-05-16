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
        $base = rtrim(config('app.url'), '/');

        $artisans   = Artisan::whereNotNull('slug')->get(['slug', 'updated_at']);
        $categories = Category::whereNotNull('slug')->get(['slug', 'updated_at']);
        $posts      = Post::whereNotNull('slug')->where('is_published', true)->get(['slug', 'updated_at']);

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
        $xml .= '        xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";

        // Static pages
        $static = [
            [''           , 'daily',   '1.0'],
            ['/search'    , 'daily',   '0.9'],
            ['/categories', 'weekly',  '0.8'],
            ['/blog'      , 'weekly',  '0.8'],
            ['/faq'       , 'monthly', '0.7'],
            ['/about'     , 'monthly', '0.6'],
            ['/contact'   , 'monthly', '0.6'],
        ];

        foreach ($static as [$path, $freq, $priority]) {
            foreach (self::LOCALES as $locale) {
                $xml .= $this->url(
                    "{$base}/{$locale}{$path}",
                    $freq,
                    $priority,
                    null,
                    $this->alternates($base, $path)
                );
            }
        }

        // Category pages
        foreach ($categories as $category) {
            foreach (self::LOCALES as $locale) {
                $xml .= $this->url(
                    "{$base}/{$locale}/categories/{$category->slug}",
                    'weekly',
                    '0.7',
                    $category->updated_at,
                    $this->alternates($base, "/categories/{$category->slug}")
                );
            }
        }

        // Artisan profile pages
        foreach ($artisans as $artisan) {
            foreach (self::LOCALES as $locale) {
                $xml .= $this->url(
                    "{$base}/{$locale}/artisan/{$artisan->slug}",
                    'weekly',
                    '0.9',
                    $artisan->updated_at,
                    $this->alternates($base, "/artisan/{$artisan->slug}")
                );
            }
        }

        // Blog posts
        foreach ($posts as $post) {
            foreach (self::LOCALES as $locale) {
                $xml .= $this->url(
                    "{$base}/{$locale}/blog/{$post->slug}",
                    'monthly',
                    '0.7',
                    $post->updated_at,
                    $this->alternates($base, "/blog/{$post->slug}")
                );
            }
        }

        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=utf-8');
    }

    private function alternates(string $base, string $path): string
    {
        $out = '';
        foreach (self::LOCALES as $locale) {
            $out .= "    <xhtml:link rel=\"alternate\" hreflang=\"{$locale}\" href=\"{$base}/{$locale}{$path}\"/>\n";
        }
        $out .= "    <xhtml:link rel=\"alternate\" hreflang=\"x-default\" href=\"{$base}/fr{$path}\"/>\n";
        return $out;
    }

    private function url(string $loc, string $freq, string $priority, ?\Carbon\Carbon $lastmod, string $alternates): string
    {
        $out  = "  <url>\n";
        $out .= "    <loc>{$loc}</loc>\n";
        $out .= "    <changefreq>{$freq}</changefreq>\n";
        $out .= "    <priority>{$priority}</priority>\n";
        if ($lastmod) {
            $out .= "    <lastmod>{$lastmod->toAtomString()}</lastmod>\n";
        }
        $out .= $alternates;
        $out .= "  </url>\n";
        return $out;
    }
}
