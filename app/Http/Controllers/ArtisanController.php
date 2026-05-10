<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Artisan;

class ArtisanController extends Controller
{
    public function mapData()
    {
        return Artisan::with('category:id,name,slug')
            ->get(['id', 'name', 'lat', 'lng', 'slug', 'category_id', 'image', 'rating'])
            ->map(fn ($a) => [
                'id'       => $a->id,
                'name'     => $a->name,
                'lat'      => $a->lat,
                'lng'      => $a->lng,
                'slug'     => $a->slug,
                'image'    => $a->image,
                'rating'   => $a->rating,
                'category' => $a->category ? [
                    'id'   => $a->category->id,
                    'name' => $a->category->name,
                    'slug' => $a->category->slug,
                ] : null,
            ]);
    }

    public function show(string $slug)
    {
        $artisan = Artisan::with(['category', 'reviews' => fn ($q) => $q->approved()->with('user')])
            ->where('slug', $slug)
            ->firstOrFail();
        
        return Inertia::render('Artisans/Show', [
            'artisan' => $artisan,
            'schema' => $this->getSchema($artisan),
        ]);
    }

    private function getSchema(Artisan $artisan)
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => $artisan->name,
            'image' => url($artisan->image),
            'description' => $artisan->bio,
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => $artisan->city,
                'streetAddress' => $artisan->location,
                'addressCountry' => 'MA',
            ],
            'geo' => [
                '@type' => 'GeoCoordinates',
                'latitude' => $artisan->lat,
                'longitude' => $artisan->lng,
            ],
            'serviceType' => $artisan->category->name,
            'aggregateRating' => [
                '@type' => 'AggregateRating',
                'ratingValue' => $artisan->average_rating,
                'reviewCount' => max(1, $artisan->reviews()->count()),
                'bestRating' => '5',
                'worstRating' => '1',
            ],
        ];
    }
}
