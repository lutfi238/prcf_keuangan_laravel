<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'description'];

    /**
     * Get a setting value by key
     */
    public static function get(string $key, $default = null)
    {
        $setting = Cache::remember("system_setting_{$key}", 3600, function () use ($key) {
            return self::where('key', $key)->first();
        });

        if (!$setting) {
            return $default;
        }

        return self::castValue($setting->value, $setting->type);
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, $value, string $type = 'string'): void
    {
        self::updateOrCreate(
            ['key' => $key],
            ['value' => (string) $value, 'type' => $type]
        );

        Cache::forget("system_setting_{$key}");
    }

    /**
     * Cast value based on type
     */
    private static function castValue($value, string $type)
    {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'json' => json_decode($value, true),
            default => $value,
        };
    }

    /**
     * Check if maintenance mode is enabled
     */
    public static function isMaintenanceMode(): bool
    {
        return self::get('maintenance_mode', false);
    }

    /**
     * Check if registration is enabled
     */
    public static function isRegistrationEnabled(): bool
    {
        return self::get('registration_enabled', true);
    }

    /**
     * Toggle maintenance mode
     */
    public static function toggleMaintenanceMode(): bool
    {
        $current = self::isMaintenanceMode();
        self::set('maintenance_mode', !$current ? 'true' : 'false', 'boolean');
        return !$current;
    }

    /**
     * Toggle registration
     */
    public static function toggleRegistration(): bool
    {
        $current = self::isRegistrationEnabled();
        self::set('registration_enabled', !$current ? 'true' : 'false', 'boolean');
        return !$current;
    }
}
