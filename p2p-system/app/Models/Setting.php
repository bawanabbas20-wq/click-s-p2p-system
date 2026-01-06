<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;
    
    protected $fillable = ['key', 'value', 'type', 'group'];

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, $default = null)
    {
        $settings = self::getAllCached();
        return $settings[$key] ?? $default;
    }

    /**
     * Set a setting value.
     */
    public static function set(string $key, $value, string $type = 'string', string $group = 'general'): void
    {
        self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type, 'group' => $group]
        );
        
        Cache::forget('app_settings');
    }

    /**
     * Get all settings as key-value array (cached).
     */
    public static function getAllCached(): array
    {
        return Cache::remember('app_settings', 3600, function () {
            return self::pluck('value', 'key')->toArray();
        });
    }

    /**
     * Clear settings cache.
     */
    public static function clearCache(): void
    {
        Cache::forget('app_settings');
    }

    /**
     * Seed default settings if they don't exist.
     */
    public static function seedDefaults(): void
    {
        $defaults = [
            // Branding
            ['key' => 'company_name', 'value' => 'Click P2P', 'type' => 'string', 'group' => 'branding'],
            ['key' => 'company_logo', 'value' => '', 'type' => 'string', 'group' => 'branding'],
            ['key' => 'primary_color', 'value' => '#65C34A', 'type' => 'string', 'group' => 'branding'],
            ['key' => 'secondary_color', 'value' => '#1F6BFF', 'type' => 'string', 'group' => 'branding'],
            
            // Email
            ['key' => 'email_from_name', 'value' => 'Click P2P System', 'type' => 'string', 'group' => 'email'],
            ['key' => 'email_from_address', 'value' => '', 'type' => 'string', 'group' => 'email'],
            
            // General
            ['key' => 'default_currency', 'value' => 'USD', 'type' => 'string', 'group' => 'general'],
            ['key' => 'default_locale', 'value' => 'en', 'type' => 'string', 'group' => 'general'],
            ['key' => 'timezone', 'value' => 'Asia/Baghdad', 'type' => 'string', 'group' => 'general'],
        ];

        foreach ($defaults as $setting) {
            self::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}

