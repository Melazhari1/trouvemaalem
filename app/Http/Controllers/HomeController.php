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
        $artisans = Artisan::with('categories')->get();
        $topArtisans = Artisan::with('categories')
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
            'categories'  => $categories,
            'artisans'    => $artisans,
            'topArtisans' => $topArtisans,
            'cities'      => $cities,
            'topFaqs'     => $topFaqs,
            'schema'      => $this->getSchema(),
        ]);
    }

    private function getSchema(): array
    {
        $baseUrl = config('app.url');
        $locale  = app()->getLocale();

        return [
            '@context' => 'https://schema.org',
            '@graph'   => [
                [
                    '@type'       => 'WebSite',
                    '@id'         => "{$baseUrl}/#website",
                    'url'         => "{$baseUrl}/{$locale}/",
                    'name'        => 'TrouveMaalem',
                    'description' => 'La plateforme N°1 pour trouver des artisans qualifiés au Maroc.',
                    'inLanguage'  => ['fr', 'ar', 'en'],
                    'publisher'   => ['@id' => "{$baseUrl}/#organization"],
                    'potentialAction' => [
                        '@type'  => 'SearchAction',
                        'target' => [
                            '@type'       => 'EntryPoint',
                            'urlTemplate' => "{$baseUrl}/{$locale}/search?search={search_term_string}",
                        ],
                        'query-input' => 'required name=search_term_string',
                    ],
                ],
                [
                    '@type'       => 'Organization',
                    '@id'         => "{$baseUrl}/#organization",
                    'name'        => 'TrouveMaalem',
                    'url'         => $baseUrl,
                    'logo'        => [
                        '@type'      => 'ImageObject',
                        '@id'        => "{$baseUrl}/#logo",
                        'url'        => "{$baseUrl}/images/logo.png",
                        'contentUrl' => "{$baseUrl}/images/logo.png",
                        'width'      => 200,
                        'height'     => 60,
                        'caption'    => 'TrouveMaalem',
                    ],
                    'image'       => ['@id' => "{$baseUrl}/#logo"],
                    'description' => 'Plateforme marocaine de mise en relation entre particuliers et artisans qualifiés: plombiers, électriciens, peintres, maçons, menuisiers.',
                    'areaServed'  => [
                        '@type'  => 'Country',
                        'name'   => 'Morocco',
                        'sameAs' => 'https://www.wikidata.org/wiki/Q1028',
                    ],
                    'address' => [
                        '@type'           => 'PostalAddress',
                        'addressCountry'  => 'MA',
                        'addressLocality' => 'Marrakech',
                    ],
                    'contactPoint' => [
                        '@type'             => 'ContactPoint',
                        'contactType'       => 'customer support',
                        'email'             => 'contact@trouvemaalem.com',
                        'availableLanguage' => [
                            ['@type' => 'Language', 'name' => 'French'],
                            ['@type' => 'Language', 'name' => 'Arabic'],
                            ['@type' => 'Language', 'name' => 'English'],
                        ],
                    ],
                    'sameAs' => [
                        'https://www.facebook.com/trouvemaalem',
                        'https://www.instagram.com/trouvemaalem',
                    ],
                ],
                [
                    '@type'       => 'WebPage',
                    '@id'         => "{$baseUrl}/{$locale}/#webpage",
                    'url'         => "{$baseUrl}/{$locale}/",
                    'name'        => 'TrouveMaalem — Artisans Experts & Services Premium au Maroc',
                    'description' => 'Trouvez des artisans qualifiés au Maroc: plombiers, électriciens, peintres, maçons — vérifiés et notés par de vrais clients.',
                    'inLanguage'  => $locale,
                    'isPartOf'    => ['@id' => "{$baseUrl}/#website"],
                    'about'       => ['@id' => "{$baseUrl}/#organization"],
                    'breadcrumb'  => [
                        '@type'           => 'BreadcrumbList',
                        'itemListElement' => [
                            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Accueil', 'item' => "{$baseUrl}/{$locale}/"],
                        ],
                    ],
                ],
            ],
        ];
    }
}
