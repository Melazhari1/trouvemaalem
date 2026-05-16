<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Artisan extends Model
{
    use HasTranslations;

    public array $translatable = ['name', 'bio'];

    protected $fillable = ['slug', 'city', 'lat', 'lng', 'image', 'rating', 'phone', 'whatsapp', 'is_verified', 'locations'];

    protected $casts = [
        'lat'         => 'float',
        'lng'         => 'float',
        'rating'      => 'float',
        'is_verified' => 'boolean',
        'locations'   => 'array',
    ];

    protected $appends = ['average_rating'];

    // --- Virtual getters for Filament form fields ---

    public function getNameEnAttribute(): string { return $this->getTranslation('name', 'en', false) ?? ''; }
    public function getNameFrAttribute(): string { return $this->getTranslation('name', 'fr', false) ?? ''; }
    public function getNameArAttribute(): string { return $this->getTranslation('name', 'ar', false) ?? ''; }

    public function getBioEnAttribute(): string { return $this->getTranslation('bio', 'en', false) ?? ''; }
    public function getBioFrAttribute(): string { return $this->getTranslation('bio', 'fr', false) ?? ''; }
    public function getBioArAttribute(): string { return $this->getTranslation('bio', 'ar', false) ?? ''; }

    // --- fill() override: maps name_en → setTranslation('name','en',...) ---

    public function fill(array $attributes): static
    {
        foreach ($this->translatable as $field) {
            foreach (['en', 'fr', 'ar'] as $locale) {
                $key = "{$field}_{$locale}";
                if (array_key_exists($key, $attributes)) {
                    $this->setTranslation($field, $locale, $attributes[$key] ?? '');
                    unset($attributes[$key]);
                }
            }
        }

        return parent::fill($attributes);
    }

    public function attributesToArray(): array
    {
        $attributes = parent::attributesToArray();
        $locale     = app()->getLocale();

        foreach ($this->translatable as $field) {
            if (array_key_exists($field, $attributes)) {
                $attributes[$field] = $this->getTranslation($field, $locale, true);
            }
        }

        // Compute a single display string from the locations array (for FE and schema.org)
        $attributes['location'] = collect($attributes['locations'] ?? [])
            ->map(fn ($loc) => $loc[$locale] ?? $loc['en'] ?? '')
            ->filter()
            ->implode(', ');

        if (!empty($attributes['image']) && !str_starts_with($attributes['image'], 'http')) {
            $attributes['image'] = \Illuminate\Support\Facades\Storage::disk('public')->url($attributes['image']);
        }

        return $attributes;
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getAverageRatingAttribute()
    {
        return round($this->reviews()->approved()->avg('rating') ?? 0.0, 1);
    }
}
