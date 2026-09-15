<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $guarded = ['id'];

    /** Sensible defaults used until an admin saves their own. */
    public const DEFAULTS = [
        'company_name' => 'Window Trip',
        'tagline' => 'Your Complete Travel Partner',
        'support_email' => 'hello@windowtrip.test',
        'support_phone' => '+880 1700-000000',
        'office_address' => 'Dhaka, Bangladesh',
        'office_hours' => 'Sat–Thu, 10am – 7pm',
        'alert_email' => 'ops@windowtrip.test',
        'facebook_url' => '',
        'instagram_url' => '',
        'whatsapp_number' => '',
        // Mail / SMTP
        'mail_mailer' => 'log',          // 'smtp' to send for real, 'log' to capture only
        'mail_host' => '',
        'mail_port' => '587',
        'mail_username' => '',
        'mail_encryption' => 'tls',      // tls | ssl | '' (none)
        'mail_from_address' => 'no-reply@windowtrip.test',
        'mail_from_name' => 'Window Trip',
    ];

    public static function get(string $key, $default = null)
    {
        $all = Cache::rememberForever('settings.all', fn () => static::pluck('value', 'key')->toArray());

        return $all[$key] ?? $default ?? (self::DEFAULTS[$key] ?? null);
    }

    public static function set(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget('settings.all');
    }

    /** All settings merged over defaults. */
    public static function allWithDefaults(): array
    {
        return array_merge(self::DEFAULTS, static::pluck('value', 'key')->toArray());
    }
}
