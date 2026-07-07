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

        // Automatically decode JSON arrays or objects
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && (is_array($decoded) || is_object($decoded))) {
                $value = $decoded;
            }
        }

        // Fallback to default if caller expects an array but value is not an array
        if (is_array($default) && !is_array($value)) {
            $value = $default;
        }

        static::$cachedSettings[$key] = $value;

        return $value;
    }

    public static function set(string $key, $value): self
    {
        $dbValue = (is_array($value) || is_object($value)) ? json_encode($value) : $value;
        $setting = static::updateOrCreate(['key' => $key], ['value' => $dbValue]);
        static::$cachedSettings[$key] = $value;
        return $setting;
    }
}
