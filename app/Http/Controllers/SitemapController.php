<?php

namespace App\Http\Controllers;

use App\Models\Artisan;
use App\Models\Category;
use App\Models\Post;

class SitemapController extends Controller
{
    private const LOCALES = ['fr', 'en', 'ar'];

    private const STATIC_PAGES = [
        [''            , 'daily',   '1.0'],
        ['/search'     , 'daily',   '0.9'],
        ['/categories' , 'weekly',  '0.8'],
        ['/blog'       , 'weekly',  '0.8'],
        ['/faq'        , 'monthly', '0.7'],
        ['/about'      , 'monthly', '0.6'],
        ['/contact'    , 'monthly', '0.6'],
    ];

    /** Sitemap index — lists one child sitemap per locale */
    public function index()
    {
        $base = rtrim(request()->getSchemeAndHttpHost(), '/');
        $now  = now()->toAtomString();

        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach (self::LOCALES as $locale) {
            $xml .= "  <sitemap>\n";
            $xml .= "    <loc>{$base}/sitemap-{$locale}.xml</loc>\n";
            $xml .= "    <lastmod>{$now}</lastmod>\n";
            $xml .= "  </sitemap>\n";
        }

        $xml .= '</sitemapindex>';

        return $this->xmlResponse($xml);
    }

    /** Per-locale sitemap */
    public function locale(string $locale)
    {
        abort_if(! in_array($locale, self::LOCALES), 404);

        $base = rtrim(request()->getSchemeAndHttpHost(), '/');

        $artisans   = Artisan::whereNotNull('slug')->get(['slug', 'updated_at']);
        $categories = Category::whereNotNull('slug')->get(['slug', 'updated_at']);
        $posts      = Post::whereNotNull('slug')->where('is_published', true)->get(['slug', 'updated_at']);

        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
        $xml .= '        xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";

        foreach (self::STATIC_PAGES as [$path, $freq, $priority]) {
            $xml .= $this->url("{$base}/{$locale}{$path}", $freq, $priority, null, $this->alternates($base, $path));
        }

        foreach ($categories as $item) {
            $xml .= $this->url("{$base}/{$locale}/categories/{$item->slug}", 'weekly', '0.7', $item->updated_at, $this->alternates($base, "/categories/{$item->slug}"));
        }

        foreach ($artisans as $item) {
            $xml .= $this->url("{$base}/{$locale}/artisan/{$item->slug}", 'weekly', '0.9', $item->updated_at, $this->alternates($base, "/artisan/{$item->slug}"));
        }

        foreach ($posts as $item) {
            $xml .= $this->url("{$base}/{$locale}/blog/{$item->slug}", 'monthly', '0.7', $item->updated_at, $this->alternates($base, "/blog/{$item->slug}"));
        }

        $xml .= '</urlset>';

        return $this->xmlResponse($xml);
    }

    private function alternates(string $base, string $path): string
    {
        $out = '';
        foreach (self::LOCALES as $loc) {
            $out .= "    <xhtml:link rel=\"alternate\" hreflang=\"{$loc}\" href=\"{$base}/{$loc}{$path}\"/>\n";
        }
        $out .= "    <xhtml:link rel=\"alternate\" hreflang=\"x-default\" href=\"{$base}/fr{$path}\"/>\n";
        return $out;
    }

    private function url(string $loc, string $freq, string $priority, $lastmod, string $alternates): string
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

    private function xmlResponse(string $xml)
    {
        return response($xml, 200)->header('Content-Type', 'application/xml; charset=utf-8');
    }
}
