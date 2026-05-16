<?xml version="1.0" encoding="UTF-8"?>
<urlset
    xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:xhtml="http://www.w3.org/1999/xhtml">

    {{-- ── Static pages ─────────────────────────────────────────────────────── --}}
    @php
        $base = rtrim(config('app.url'), '/');
        $staticPages = [
            ['path' => '',          'changefreq' => 'daily',   'priority' => '1.0'],
            ['path' => '/search',   'changefreq' => 'daily',   'priority' => '0.9'],
            ['path' => '/categories','changefreq' => 'weekly',  'priority' => '0.8'],
            ['path' => '/blog',     'changefreq' => 'weekly',  'priority' => '0.8'],
            ['path' => '/faq',      'changefreq' => 'monthly', 'priority' => '0.7'],
            ['path' => '/about',    'changefreq' => 'monthly', 'priority' => '0.6'],
            ['path' => '/contact',  'changefreq' => 'monthly', 'priority' => '0.6'],
        ];
    @endphp

    @foreach($staticPages as $page)
        @foreach($locales as $locale)
    <url>
        <loc>{{ $base }}/{{ $locale }}{{ $page['path'] }}</loc>
        <changefreq>{{ $page['changefreq'] }}</changefreq>
        <priority>{{ $page['priority'] }}</priority>
            @foreach($locales as $alt)
        <xhtml:link rel="alternate" hreflang="{{ $alt }}" href="{{ $base }}/{{ $alt }}{{ $page['path'] }}"/>
            @endforeach
        <xhtml:link rel="alternate" hreflang="x-default" href="{{ $base }}/fr{{ $page['path'] }}"/>
    </url>
        @endforeach
    @endforeach

    {{-- ── Category pages ──────────────────────────────────────────────────── --}}
    @foreach($categories as $category)
        @foreach($locales as $locale)
    <url>
        <loc>{{ $base }}/{{ $locale }}/categories/{{ $category->slug }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
        <lastmod>{{ $category->updated_at->toAtomString() }}</lastmod>
            @foreach($locales as $alt)
        <xhtml:link rel="alternate" hreflang="{{ $alt }}" href="{{ $base }}/{{ $alt }}/categories/{{ $category->slug }}"/>
            @endforeach
        <xhtml:link rel="alternate" hreflang="x-default" href="{{ $base }}/fr/categories/{{ $category->slug }}"/>
    </url>
        @endforeach
    @endforeach

    {{-- ── Artisan profile pages ───────────────────────────────────────────── --}}
    @foreach($artisans as $artisan)
        @foreach($locales as $locale)
    <url>
        <loc>{{ $base }}/{{ $locale }}/artisan/{{ $artisan->slug }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
        <lastmod>{{ $artisan->updated_at->toAtomString() }}</lastmod>
            @foreach($locales as $alt)
        <xhtml:link rel="alternate" hreflang="{{ $alt }}" href="{{ $base }}/{{ $alt }}/artisan/{{ $artisan->slug }}"/>
            @endforeach
        <xhtml:link rel="alternate" hreflang="x-default" href="{{ $base }}/fr/artisan/{{ $artisan->slug }}"/>
    </url>
        @endforeach
    @endforeach

    {{-- ── Blog posts ──────────────────────────────────────────────────────── --}}
    @foreach($posts as $post)
        @foreach($locales as $locale)
    <url>
        <loc>{{ $base }}/{{ $locale }}/blog/{{ $post->slug }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
        <lastmod>{{ $post->updated_at->toAtomString() }}</lastmod>
            @foreach($locales as $alt)
        <xhtml:link rel="alternate" hreflang="{{ $alt }}" href="{{ $base }}/{{ $alt }}/blog/{{ $post->slug }}"/>
            @endforeach
        <xhtml:link rel="alternate" hreflang="x-default" href="{{ $base }}/fr/blog/{{ $post->slug }}"/>
    </url>
        @endforeach
    @endforeach

</urlset>
