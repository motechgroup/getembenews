<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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

        $setting = static::where('key', $key)->first();
        $value = $setting ? $setting->value : $default;

        static::$cachedSettings[$key] = $value;

        return $value;
    }

    public static function set(string $key, $value): self
    {
        $setting = static::updateOrCreate(['key' => $key], ['value' => $value]);
        static::$cachedSettings[$key] = $value;
        return $setting;
    }
}
