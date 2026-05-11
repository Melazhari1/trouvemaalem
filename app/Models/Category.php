<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Category extends Model
{
    use HasTranslations;

    public array $translatable = ['name', 'description'];

    protected $fillable = ['slug', 'image'];

    // --- Virtual getters for Filament form fields ---

    public function getNameEnAttribute(): string { return $this->getTranslation('name', 'en', false) ?? ''; }
    public function getNameFrAttribute(): string { return $this->getTranslation('name', 'fr', false) ?? ''; }
    public function getNameArAttribute(): string { return $this->getTranslation('name', 'ar', false) ?? ''; }

    public function getDescriptionEnAttribute(): string { return $this->getTranslation('description', 'en', false) ?? ''; }
    public function getDescriptionFrAttribute(): string { return $this->getTranslation('description', 'fr', false) ?? ''; }
    public function getDescriptionArAttribute(): string { return $this->getTranslation('description', 'ar', false) ?? ''; }

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

    public function artisans()
    {
        return $this->hasMany(Artisan::class);
    }
}
