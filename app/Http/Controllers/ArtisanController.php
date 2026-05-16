<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use App\Models\Artisan;

class ArtisanController extends Controller
{
    public function mapData()
    {
        return Artisan::with('categories:id,name,slug')
            ->get(['id', 'name', 'lat', 'lng', 'slug', 'image', 'rating'])
            ->map(fn ($a) => [
                'id'       => $a->id,
                'name'     => $a->name,
                'lat'      => $a->lat,
                'lng'      => $a->lng,
                'slug'     => $a->slug,
                'image'    => $a->image && !str_starts_with($a->image, 'http') ? Storage::disk('public')->url($a->image) : $a->image,
                'rating'   => $a->rating,
                'category' => $a->categories->first() ? [
                    'id'   => $a->categories->first()->id,
                    'name' => $a->categories->first()->name,
                    'slug' => $a->categories->first()->slug,
                ] : null,
            ]);
    }

    public function show(string $slug)
    {
        $artisan = Artisan::with(['categories', 'reviews' => fn ($q) => $q->approved()->with('user')])
            ->where('slug', $slug)
            ->firstOrFail();
        
        return Inertia::render('Artisans/Show', [
            'artisan' => $artisan,
            'schema' => $this->getSchema($artisan),
        ]);
    }

    private function getSchema(Artisan $artisan): array
    {
        $primaryCategory = $artisan->categories->first();
        $approvedReviews = $artisan->reviews->take(3);
        $approvedCount   = $artisan->reviews->count();
        $locale          = app()->getLocale();
        $baseUrl         = config('app.url');

        $imageUrl = null;
        if ($artisan->image) {
            $imageUrl = str_starts_with($artisan->image, 'http')
                ? $artisan->image
                : Storage::disk('public')->url($artisan->image);
        }

        $profileUrl = "{$baseUrl}/{$locale}/artisan/{$artisan->slug}";

        $businessNode = [
            '@type'       => ['LocalBusiness', 'ProfessionalService'],
            '@id'         => "{$profileUrl}#business",
            'name'        => $artisan->name,
            'description' => $artisan->bio,
            'url'         => $profileUrl,
            'address'     => [
                '@type'           => 'PostalAddress',
                'streetAddress'   => $artisan->location,
                'addressLocality' => $artisan->city,
                'addressCountry'  => 'MA',
            ],
            'priceRange'         => 'MAD',
            'currenciesAccepted' => 'MAD',
            'paymentAccepted'    => 'Cash',
            'isPartOf'           => ['@id' => "{$baseUrl}/#organization"],
        ];

        if ($imageUrl) {
            $businessNode['image'] = [
                '@type'  => 'ImageObject',
                'url'    => $imageUrl,
                'width'  => 400,
                'height' => 400,
            ];
        }

        if ($artisan->lat && $artisan->lng) {
            $businessNode['geo'] = [
                '@type'     => 'GeoCoordinates',
                'latitude'  => (float) $artisan->lat,
                'longitude' => (float) $artisan->lng,
            ];
            $businessNode['hasMap']    = "https://maps.google.com/?q={$artisan->lat},{$artisan->lng}";
            $businessNode['areaServed'] = ['@type' => 'City', 'name' => $artisan->city];
        }

        if ($artisan->phone) {
            $businessNode['telephone'] = $artisan->phone;
        }

        if ($primaryCategory) {
            $businessNode['serviceType']    = $primaryCategory->name;
            $businessNode['hasOfferCatalog'] = [
                '@type' => 'OfferCatalog',
                'name'  => $primaryCategory->name,
            ];
        }

        // Only emit aggregateRating when there are real approved reviews
        if ($approvedCount > 0) {
            $businessNode['aggregateRating'] = [
                '@type'       => 'AggregateRating',
                'ratingValue' => round((float) $artisan->average_rating, 1),
                'reviewCount' => $approvedCount,
                'bestRating'  => '5',
                'worstRating' => '1',
            ];
        }

        if ($approvedReviews->isNotEmpty()) {
            $businessNode['review'] = $approvedReviews->map(fn ($r) => [
                '@type'  => 'Review',
                'author' => [
                    '@type' => 'Person',
                    'name'  => $r->submitted_by_name ?? ($r->user?->name ?? 'Anonyme'),
                ],
                'reviewRating' => [
                    '@type'       => 'Rating',
                    'ratingValue' => (string) $r->rating,
                    'bestRating'  => '5',
                    'worstRating' => '1',
                ],
                'reviewBody'    => $r->comment,
                'datePublished' => $r->created_at->toDateString(),
            ])->toArray();
        }

        $breadcrumbs = [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Accueil', 'item' => "{$baseUrl}/{$locale}/"],
        ];
        $pos = 2;
        if ($primaryCategory) {
            $breadcrumbs[] = ['@type' => 'ListItem', 'position' => $pos++, 'name' => $primaryCategory->name, 'item' => "{$baseUrl}/{$locale}/categories/{$primaryCategory->slug}"];
        }
        $breadcrumbs[] = ['@type' => 'ListItem', 'position' => $pos, 'name' => $artisan->name, 'item' => $profileUrl];

        $pageNode = [
            '@type'        => 'ProfilePage',
            '@id'          => "{$profileUrl}#webpage",
            'url'          => $profileUrl,
            'name'         => $artisan->name . ($primaryCategory ? " — {$primaryCategory->name}" : '') . " à {$artisan->city} | TrouveMaalem",
            'description'  => $artisan->bio,
            'inLanguage'   => $locale,
            'isPartOf'     => ['@id' => "{$baseUrl}/#website"],
            'about'        => ['@id' => "{$profileUrl}#business"],
            'dateModified' => $artisan->updated_at?->toIso8601String(),
            'breadcrumb'   => [
                '@type'           => 'BreadcrumbList',
                'itemListElement' => $breadcrumbs,
            ],
        ];

        return [
            '@context' => 'https://schema.org',
            '@graph'   => [$businessNode, $pageNode],
        ];
    }
}
