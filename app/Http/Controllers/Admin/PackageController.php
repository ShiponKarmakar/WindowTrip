<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PackageController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Packages/Index', [
            'packages' => Package::orderBy('sort_order')->orderBy('title')->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Packages/Form', ['package' => null]);
    }

    public function edit(Package $package)
    {
        return Inertia::render('Admin/Packages/Form', ['package' => $package]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        Package::create($data);

        return redirect()->route('admin.packages.index')->with('success', $data['title'].' added.');
    }

    public function update(Request $request, Package $package)
    {
        $data = $this->validateData($request, $package->id);
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        $package->update($data);

        // Stay on the edit page so the admin can keep working.
        return redirect()->route('admin.packages.edit', $package->id)->with('success', $package->title.' updated.');
    }

    public function destroy(Package $package)
    {
        $package->delete();

        return back()->with('success', 'Package removed.');
    }

    private function validateData(Request $request, ?int $ignore = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:100', 'alpha_dash', Rule::unique('packages', 'slug')->ignore($ignore)],
            'destination' => ['nullable', 'string', 'max:80'],
            'nights' => ['nullable', 'string', 'max:30'],
            'price' => ['nullable', 'string', 'max:30'],
            'tag' => ['nullable', 'string', 'max:40'],
            'color' => ['nullable', 'string', 'max:20'],
            'active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'includes' => ['nullable', 'array'],
            'includes.*' => ['nullable', 'string', 'max:120'],
        ]);
    }
}
