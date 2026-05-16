<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('artisans')->get();
        return Inertia::render('Categories/Index', [
            'categories' => $categories,
            'schema'     => $this->getIndexSchema($categories),
        ]);
    }

    public function show(string $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $artisans = \App\Models\Artisan::whereHas('categories', fn ($q) => $q->where('categories.id', $category->id))
            ->with('categories')
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Categories/Show', [
            'category' => $category,
            'artisans' => $artisans,
            'schema'   => $this->getShowSchema($category, $artisans),
        ]);
    }

    private function getIndexSchema($categories): array
    {
        $baseUrl = config('app.url');
        $locale  = app()->getLocale();
        $pageUrl = "{$baseUrl}/{$locale}/categories";

        return [
            '@context' => 'https://schema.org',
            '@graph'   => [
                [
                    '@type'       => 'CollectionPage',
                    '@id'         => "{$pageUrl}#webpage",
                    'url'         => $pageUrl,
                    'name'        => 'Toutes les Catégories — TrouveMaalem',
                    'description' => "Découvrez toutes les catégories d'artisans disponibles sur TrouveMaalem au Maroc.",
                    'inLanguage'  => $locale,
                    'isPartOf'    => ['@id' => "{$baseUrl}/#website"],
                    'breadcrumb'  => [
                        '@type'           => 'BreadcrumbList',
                        'itemListElement' => [
                            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Accueil',    'item' => "{$baseUrl}/{$locale}/"],
                            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Catégories', 'item' => $pageUrl],
                        ],
                    ],
                    'mainEntity' => [
                        '@type'           => 'ItemList',
                        'numberOfItems'   => $categories->count(),
                        'itemListElement' => $categories->values()->map(fn ($cat, $i) => [
                            '@type'    => 'ListItem',
                            'position' => $i + 1,
                            'item'     => [
                                '@type' => 'Service',
                                'name'  => $cat->name,
                                'url'   => "{$baseUrl}/{$locale}/categories/{$cat->slug}",
                            ],
                        ])->toArray(),
                    ],
                ],
            ],
        ];
    }

    private function getShowSchema(Category $category, $artisans): array
    {
        $baseUrl    = config('app.url');
        $locale     = app()->getLocale();
        $pageUrl    = "{$baseUrl}/{$locale}/categories/{$category->slug}";
        $totalCount = $artisans->total();

        $listItems = $artisans->getCollection()->values()->map(fn ($a, $i) => [
            '@type'    => 'ListItem',
            'position' => $i + 1,
            'item'     => [
                '@type'   => 'LocalBusiness',
                '@id'     => "{$baseUrl}/{$locale}/artisan/{$a->slug}#business",
                'name'    => $a->name,
                'url'     => "{$baseUrl}/{$locale}/artisan/{$a->slug}",
                'address' => [
                    '@type'           => 'PostalAddress',
                    'addressLocality' => $a->city,
                    'addressCountry'  => 'MA',
                ],
            ],
        ])->toArray();

        return [
            '@context' => 'https://schema.org',
            '@graph'   => [
                [
                    '@type'       => 'CollectionPage',
                    '@id'         => "{$pageUrl}#webpage",
                    'url'         => $pageUrl,
                    'name'        => "{$category->name} au Maroc — TrouveMaalem",
                    'description' => $category->description ?? "Trouvez les meilleurs {$category->name} au Maroc sur TrouveMaalem.",
                    'inLanguage'  => $locale,
                    'isPartOf'    => ['@id' => "{$baseUrl}/#website"],
                    'about'       => ['@id' => "{$pageUrl}#service"],
                    'breadcrumb'  => [
                        '@type'           => 'BreadcrumbList',
                        'itemListElement' => [
                            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Accueil',       'item' => "{$baseUrl}/{$locale}/"],
                            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Catégories',    'item' => "{$baseUrl}/{$locale}/categories"],
                            ['@type' => 'ListItem', 'position' => 3, 'name' => $category->name, 'item' => $pageUrl],
                        ],
                    ],
                    'mainEntity' => [
                        '@type'           => 'ItemList',
                        '@id'             => "{$pageUrl}#list",
                        'name'            => "{$category->name} certifiés au Maroc",
                        'numberOfItems'   => $totalCount,
                        'itemListOrder'   => 'https://schema.org/ItemListOrderDescending',
                        'itemListElement' => $listItems,
                    ],
                ],
                [
                    '@type'       => 'Service',
                    '@id'         => "{$pageUrl}#service",
                    'name'        => $category->name,
                    'description' => $category->description ?? "Services de {$category->name} professionnels au Maroc.",
                    'provider'    => ['@id' => "{$baseUrl}/#organization"],
                    'serviceType' => $category->name,
                    'areaServed'  => [
                        '@type'  => 'Country',
                        'name'   => 'Morocco',
                        'sameAs' => 'https://www.wikidata.org/wiki/Q1028',
                    ],
                ],
            ],
        ];
    }
}
