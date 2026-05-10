<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminSetting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'description'];

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = self::where('key', $key)->first();

        if (! $setting) {
            return $default;
        }

        if ($setting->type === 'boolean') {
            return (bool) $setting->value;
        }

        if ($setting->type === 'json') {
            return $setting->value ? json_decode($setting->value, true) : $default;
        }

        return $setting->value ?? $default;
    }

    public static function set(string $key, mixed $value): self
    {
        return self::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
