<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VisaCountry;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class VisaCountryController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Visas/Index', [
            'countries' => VisaCountry::orderBy('sort_order')->orderBy('name')->get()
                ->map(fn ($c) => [
                    'id' => $c->id,
                    'name' => $c->name,
                    'slug' => $c->slug,
                    'flag' => $c->flag,
                    'subtitle' => $c->subtitle,
                    'fee_from' => $c->fee_from,
                    'processing' => $c->processing,
                    'active' => $c->active,
                    'documents' => count($c->documents ?? []),
                ]),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Visas/Form', ['country' => null]);
    }

    public function edit(VisaCountry $visa)
    {
        return Inertia::render('Admin/Visas/Form', ['country' => $visa]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        VisaCountry::create($data);

        return redirect()->route('admin.visas.index')->with('success', $data['name'].' visa added.');
    }

    public function update(Request $request, VisaCountry $visa)
    {
        $data = $this->validateData($request, $visa->id);
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $visa->update($data);

        // Stay on the edit page so the admin can keep working.
        return redirect()->route('admin.visas.edit', $visa->id)->with('success', $visa->name.' visa updated.');
    }

    public function destroy(VisaCountry $visa)
    {
        $visa->delete();

        return back()->with('success', 'Visa destination removed.');
    }

    private function validateData(Request $request, ?int $ignore = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'slug' => ['nullable', 'string', 'max:80', 'alpha_dash', Rule::unique('visa_countries', 'slug')->ignore($ignore)],
            'flag' => ['nullable', 'string', 'max:10'],
            'subtitle' => ['nullable', 'string', 'max:100'],
            'visa_types' => ['nullable', 'array'],
            'visa_types.*' => ['nullable', 'string', 'max:60'],
            'processing' => ['nullable', 'string', 'max:60'],
            'validity' => ['nullable', 'string', 'max:60'],
            'stay' => ['nullable', 'string', 'max:60'],
            'fee_from' => ['nullable', 'string', 'max:30'],
            'overview' => ['nullable', 'string', 'max:3000'],
            'photo_spec' => ['nullable', 'string', 'max:300'],
            'active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'requirements' => ['nullable', 'array'],
            'requirements.*' => ['nullable', 'string', 'max:500'],
            'documents' => ['nullable', 'array'],
            'documents.*.title' => ['nullable', 'string', 'max:120'],
            'documents.*.desc' => ['nullable', 'string', 'max:500'],
            'faqs' => ['nullable', 'array'],
            'faqs.*.q' => ['nullable', 'string', 'max:300'],
            'faqs.*.a' => ['nullable', 'string', 'max:1500'],
        ]);
    }
}
