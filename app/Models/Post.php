<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Post extends Model
{
    use HasTranslations;

    public array $translatable = ['title', 'excerpt', 'content'];

    protected $fillable = ['slug', 'image', 'is_published'];

    // --- Virtual getters for Filament form fields ---

    public function getTitleEnAttribute(): string { return $this->getTranslation('title', 'en', false) ?? ''; }
    public function getTitleFrAttribute(): string { return $this->getTranslation('title', 'fr', false) ?? ''; }
    public function getTitleArAttribute(): string { return $this->getTranslation('title', 'ar', false) ?? ''; }

    public function getExcerptEnAttribute(): string { return $this->getTranslation('excerpt', 'en', false) ?? ''; }
    public function getExcerptFrAttribute(): string { return $this->getTranslation('excerpt', 'fr', false) ?? ''; }
    public function getExcerptArAttribute(): string { return $this->getTranslation('excerpt', 'ar', false) ?? ''; }

    public function getContentEnAttribute(): string { return $this->getTranslation('content', 'en', false) ?? ''; }
    public function getContentFrAttribute(): string { return $this->getTranslation('content', 'fr', false) ?? ''; }
    public function getContentArAttribute(): string { return $this->getTranslation('content', 'ar', false) ?? ''; }

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

    // --- fill() override: maps title_en → setTranslation('title','en',...) ---

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
}
