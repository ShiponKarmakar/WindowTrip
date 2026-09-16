<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
                // Admin guard runs independently of the customer (web) session.
                'admin' => Auth::guard('admin')->user(),
                'isAdmin' => (bool) Auth::guard('admin')->user()?->hasAnyRole(['admin', 'agent']),
                'adminRole' => Auth::guard('admin')->user()?->getRoleNames()->first(),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            // Active visa destinations for the public header dropdown / mobile menu.
            'navVisas' => fn () => collect(\App\Models\VisaCountry::publicConfig())
                ->map(fn ($v, $slug) => [
                    'slug' => $slug,
                    'name' => $v['name'],
                    'flag' => $v['flag'],
                    'processing' => $v['processing'],
                    'fee_from' => $v['fee_from'],
                ])->values(),
            // Company/contact settings used across public footer & pages.
            'site' => fn () => [
                'company' => \App\Models\Setting::get('company_name'),
                'tagline' => \App\Models\Setting::get('tagline'),
                'email' => \App\Models\Setting::get('support_email'),
                'phone' => \App\Models\Setting::get('support_phone'),
                'address' => \App\Models\Setting::get('office_address'),
                'hours' => \App\Models\Setting::get('office_hours'),
            ],
        ];
    }
}
