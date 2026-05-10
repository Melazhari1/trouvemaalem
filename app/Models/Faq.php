<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Faq extends Model
{
    use HasTranslations;

    public array $translatable = ['question', 'answer'];

    protected $fillable = ['is_active', 'order'];

    // --- Virtual getters for Filament form fields ---

    public function getQuestionEnAttribute(): string { return $this->getTranslation('question', 'en', false) ?? ''; }
    public function getQuestionFrAttribute(): string { return $this->getTranslation('question', 'fr', false) ?? ''; }
    public function getQuestionArAttribute(): string { return $this->getTranslation('question', 'ar', false) ?? ''; }

    public function getAnswerEnAttribute(): string { return $this->getTranslation('answer', 'en', false) ?? ''; }
    public function getAnswerFrAttribute(): string { return $this->getTranslation('answer', 'fr', false) ?? ''; }
    public function getAnswerArAttribute(): string { return $this->getTranslation('answer', 'ar', false) ?? ''; }

    public function attributesToArray(): array
    {
        $attributes = parent::attributesToArray();
        $locale     = app()->getLocale();
        foreach ($this->translatable as $field) {
            if (array_key_exists($field, $attributes)) {
                $attributes[$field] = $this->getTranslation($field, $locale, true);
            }
        }
        return $attributes;
    }

    // --- fill() override: maps question_en → setTranslation('question','en',...) ---

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
