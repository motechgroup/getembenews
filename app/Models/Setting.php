<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value'];

    // Cache settings to avoid redundant queries during a single request
    protected static $cachedSettings = [];

    public static function get(string $key, $default = null)
    {
        if (array_key_exists($key, static::$cachedSettings)) {
            return static::$cachedSettings[$key];
        }

        $cacheKey = 'setting_v1_' . $key;
        $value = Cache::remember($cacheKey, 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });

        // Only decode JSON if the expected default is an array
        if (is_array($default)) {
            if (is_string($value)) {
                $decoded = json_decode($value, true);
                $value = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : $default;
            } elseif (!is_array($value)) {
                $value = $default;
            }
        }

        static::$cachedSettings[$key] = $value;

        return $value;
    }

    public static function set(string $key, $value): self
    {
        $dbValue = (is_array($value) || is_object($value)) ? json_encode($value) : $value;
        $setting = static::updateOrCreate(['key' => $key], ['value' => $dbValue]);
        
        Cache::forget('setting_v1_' . $key);
        static::$cachedSettings[$key] = $value;
        
        return $setting;
    }
}
