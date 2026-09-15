<?php

namespace Database\Seeders;

use App\Models\Package;
use App\Models\VisaCountry;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    /** Import the original config-file catalog into the database (idempotent). */
    public function run(): void
    {
        $i = 0;
        foreach (config('visa', []) as $slug => $c) {
            VisaCountry::updateOrCreate(['slug' => $slug], [
                'name' => $c['name'],
                'flag' => $c['flag'] ?? null,
                'subtitle' => $c['subtitle'] ?? null,
                'processing' => $c['processing'] ?? null,
                'validity' => $c['validity'] ?? null,
                'stay' => $c['stay'] ?? null,
                'fee_from' => $c['fee_from'] ?? null,
                'overview' => $c['overview'] ?? null,
                'requirements' => $c['requirements'] ?? [],
                'documents' => $c['documents'] ?? [],
                'photo_spec' => $c['photo_spec'] ?? null,
                'faqs' => $c['faqs'] ?? [],
                'active' => true,
                'sort_order' => $i++,
            ]);
        }

        $colors = ['#139dd5', '#0f7fae', '#2ba7da', '#0d6790', '#5cc2ec', '#114a63'];
        $j = 0;
        foreach (config('packages', []) as $p) {
            Package::updateOrCreate(['slug' => $p['slug']], [
                'title' => $p['title'],
                'destination' => $p['destination'] ?? null,
                'nights' => $p['nights'] ?? null,
                'price' => $p['price'] ?? null,
                'tag' => $p['tag'] ?? null,
                'color' => $colors[$j % count($colors)],
                'includes' => $p['includes'] ?? [],
                'active' => true,
                'sort_order' => $j++,
            ]);
        }

        $this->command?->info('Catalog seeded: '.VisaCountry::count().' visas, '.Package::count().' packages.');
    }
}
