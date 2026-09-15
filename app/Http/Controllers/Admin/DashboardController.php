<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VisaApplication;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $byStatus = VisaApplication::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')->pluck('total', 'status');

        $byCountry = VisaApplication::select('country', DB::raw('count(*) as total'))
            ->groupBy('country')->orderByDesc('total')->get()
            ->map(fn ($r) => [
                'country' => data_get(\App\Models\VisaCountry::config(), "{$r->country}.name", ucfirst($r->country)),
                'flag' => data_get(\App\Models\VisaCountry::config(), "{$r->country}.flag", ''),
                'total' => $r->total,
            ]);

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total' => VisaApplication::count(),
                'submitted' => $byStatus['submitted'] ?? 0,
                'under_review' => $byStatus['under_review'] ?? 0,
                'docs_required' => $byStatus['docs_required'] ?? 0,
                'approved' => $byStatus['approved'] ?? 0,
                'rejected' => $byStatus['rejected'] ?? 0,
            ],
            'byCountry' => $byCountry,
            'recent' => VisaApplication::latest()->take(6)->get()->map(fn ($a) => [
                'id' => $a->id,
                'reference' => $a->reference,
                'name' => $a->full_name,
                'country' => $a->countryName(),
                'status' => $a->status,
                'created' => $a->created_at->diffForHumans(),
            ]),
        ]);
    }
}
