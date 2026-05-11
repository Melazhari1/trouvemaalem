<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Artisan extends Model
{
    use HasTranslations;

    public array $translatable = ['name', 'bio', 'location'];

    protected $fillable = ['category_id', 'slug', 'city', 'lat', 'lng', 'image', 'rating', 'phone', 'is_verified'];

    protected $casts = [
        'lat'         => 'float',
        'lng'         => 'float',
        'rating'      => 'float',
        'is_verified' => 'boolean',
    ];

    protected $appends = ['average_rating'];

    // --- Virtual getters for Filament form fields ---

    public function getNameEnAttribute(): string { return $this->getTranslation('name', 'en', false) ?? ''; }
    public function getNameFrAttribute(): string { return $this->getTranslation('name', 'fr', false) ?? ''; }
    public function getNameArAttribute(): string { return $this->getTranslation('name', 'ar', false) ?? ''; }

    public function getBioEnAttribute(): string { return $this->getTranslation('bio', 'en', false) ?? ''; }
    public function getBioFrAttribute(): string { return $this->getTranslation('bio', 'fr', false) ?? ''; }
    public function getBioArAttribute(): string { return $this->getTranslation('bio', 'ar', false) ?? ''; }

    public function getLocationEnAttribute(): string { return $this->getTranslation('location', 'en', false) ?? ''; }
    public function getLocationFrAttribute(): string { return $this->getTranslation('location', 'fr', false) ?? ''; }
    public function getLocationArAttribute(): string { return $this->getTranslation('location', 'ar', false) ?? ''; }

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
        if (!empty($attributes['image']) && !str_starts_with($attributes['image'], 'http')) {
            $attributes['image'] = \Illuminate\Support\Facades\Storage::disk('public')->url($attributes['image']);
        }
        return $attributes;
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
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
