<?php

namespace App\Http\Controllers;

use App\Models\VisaCountry;

class VisaController extends Controller
{
    /**
     * Visa destinations index.
     */
    public function index()
    {
        $countries = collect(VisaCountry::publicConfig())->map(function ($data, $slug) {
            return array_merge($data, ['slug' => $slug]);
        })->values();

        return view('visa.index', compact('countries'));
    }

    /**
     * Single country visa page with requirements.
     */
    public function show(string $country)
    {
        $all = VisaCountry::config();
        $data = $all[$country] ?? null;

        abort_if(! $data || ! ($data['active'] ?? true), 404);

        $data['slug'] = $country;

        // Sibling destinations for the "other countries" strip (active only).
        $others = collect(VisaCountry::publicConfig())
            ->map(fn ($d, $slug) => array_merge($d, ['slug' => $slug]))
            ->reject(fn ($d) => $d['slug'] === $country)
            ->values();

        return view('visa.show', ['country' => $data, 'others' => $others]);
    }
}
