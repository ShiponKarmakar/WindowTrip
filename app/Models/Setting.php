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
        'brand_logo' => '',   // path under public/, e.g. brand/logo-custom.png
        'brand_icon' => '',   // path under public/, e.g. brand/icon-custom.png
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

    /** Public URL of the brand logo (uploaded one, else the bundled default). */
    public static function logoUrl(): string
    {
        $p = self::get('brand_logo');

        return $p ? '/'.ltrim($p, '/') : '/brand/logo-horizontal.svg';
    }

    /** Public URL of the brand icon / favicon. */
    public static function iconUrl(): string
    {
        $p = self::get('brand_icon');

        return $p ? '/'.ltrim($p, '/') : '/brand/icon.svg';
    }

    /** Filesystem path to a raster logo for the PDF (null if none/SVG). */
    public static function logoPdfPath(): ?string
    {
        $p = self::get('brand_logo');
        if ($p && preg_match('/\.(png|jpe?g)$/i', $p) && is_file(public_path($p))) {
            return public_path($p);
        }
        $default = public_path('brand/logo-horizontal.png');

        return is_file($default) ? $default : null;
    }
}
