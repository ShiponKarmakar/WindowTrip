<?php

namespace App\Http\Controllers;

use App\Models\VisaCountry;
use Inertia\Inertia;

class VisaController extends Controller
{
    public function index()
    {
        $countries = collect(VisaCountry::publicConfig())->map(fn ($data, $slug) => [
            'slug' => $slug,
            'name' => $data['name'],
            'flag' => $data['flag'],
            'subtitle' => $data['subtitle'],
            'overview' => $data['overview'],
            'processing' => $data['processing'],
            'fee_from' => $data['fee_from'],
        ])->values();

        return Inertia::render('Public/Visa/Index', ['countries' => $countries]);
    }

    public function show(string $country)
    {
        $all = VisaCountry::config();
        $data = $all[$country] ?? null;

        abort_if(! $data || ! ($data['active'] ?? true), 404);

        $data['slug'] = $country;

        $others = collect(VisaCountry::publicConfig())
            ->map(fn ($d, $slug) => ['slug' => $slug, 'name' => $d['name'], 'flag' => $d['flag'], 'processing' => $d['processing']])
            ->reject(fn ($d) => $d['slug'] === $country)
            ->values();

        return Inertia::render('Public/Visa/Show', ['country' => $data, 'others' => $others]);
    }
}
