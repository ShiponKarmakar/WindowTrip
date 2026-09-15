<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class VisaCountry extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'visa_types' => 'array',
            'requirements' => 'array',
            'documents' => 'array',
            'faqs' => 'array',
            'active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('visa.config'));
        static::deleted(fn () => Cache::forget('visa.config'));
    }

    /** All countries keyed by slug, in the same shape the views expect. */
    public static function config(): array
    {
        return Cache::rememberForever('visa.config', function () {
            return static::orderBy('sort_order')->orderBy('name')->get()
                ->keyBy('slug')
                ->map(fn ($c) => [
                    'name' => $c->name,
                    'flag' => $c->flag,
                    'subtitle' => $c->subtitle,
                    'visa_types' => $c->visa_types ?: ['Tourist'],
                    'processing' => $c->processing,
                    'validity' => $c->validity,
                    'stay' => $c->stay,
                    'fee_from' => $c->fee_from,
                    'overview' => $c->overview,
                    'requirements' => $c->requirements ?? [],
                    'documents' => $c->documents ?? [],
                    'photo_spec' => $c->photo_spec,
                    'faqs' => $c->faqs ?? [],
                    'active' => $c->active,
                ])->toArray();
        });
    }

    /** Active countries only, keyed by slug (for public listing). */
    public static function publicConfig(): array
    {
        return array_filter(self::config(), fn ($c) => $c['active'] ?? true);
    }
}
