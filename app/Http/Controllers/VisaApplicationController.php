<?php

namespace App\Http\Controllers;

use App\Mail\VisaApplicationAdminAlert;
use App\Mail\VisaApplicationReceived;
use App\Models\VisaApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Inertia;

class VisaApplicationController extends Controller
{
    /**
     * Show the multi-step application wizard for a country.
     */
    public function create(string $country)
    {
        $config = \App\Models\VisaCountry::config()[$country] ?? null;
        abort_if(! $config, 404);

        return Inertia::render('Visa/Apply', [
            'country' => [
                'slug' => $country,
                'name' => $config['name'],
                'flag' => $config['flag'],
                'subtitle' => $config['subtitle'],
                'visa_types' => $config['visa_types'] ?? ['Tourist'],
                'processing' => $config['processing'],
                'fee_from' => $config['fee_from'],
                'documents' => $config['documents'],
            ],
            'prefill' => [
                'full_name' => optional(auth()->user())->name,
                'email' => optional(auth()->user())->email,
            ],
        ]);
    }

    /**
     * Persist a submitted application.
     */
    public function store(Request $request, string $country)
    {
        abort_if(! isset(\App\Models\VisaCountry::config()[$country]), 404);

        $data = $request->validate([
            'visa_type' => ['required', 'string', 'max:50'],
            'travellers' => ['required', 'integer', 'min:1', 'max:20'],
            'travel_date' => ['nullable', 'date', 'after_or_equal:today'],
            'full_name' => ['required', 'string', 'max:120'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'string', 'max:20'],
            'nationality' => ['required', 'string', 'max:60'],
            'passport_number' => ['nullable', 'string', 'max:30'],
            'passport_expiry' => ['nullable', 'date', 'after:today'],
            'email' => ['required', 'email', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'passport_scan' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        $reference = 'WT-'.strtoupper(Str::random(8));

        $application = new VisaApplication();
        $application->reference = $reference;
        $application->user_id = auth()->id();
        $application->country = $country;
        $application->fill(collect($data)->only([
            'visa_type', 'travellers', 'travel_date', 'full_name', 'date_of_birth',
            'gender', 'nationality', 'passport_number', 'passport_expiry',
            'email', 'phone', 'notes',
        ])->toArray());

        // Store documents privately (not web-accessible directly).
        if ($request->hasFile('passport_scan')) {
            $application->passport_scan_path = $request->file('passport_scan')
                ->store("applications/{$reference}", 'local');
        }
        if ($request->hasFile('photo')) {
            $application->photo_path = $request->file('photo')
                ->store("applications/{$reference}", 'local');
        }

        $application->save();

        // Notify the applicant and the agency (mail driver = log in dev).
        try {
            $adminEmail = \App\Models\Setting::get('alert_email', config('mail.admin_address'));
            Mail::to($application->email)->send(new VisaApplicationReceived($application));
            Mail::to($adminEmail)->send(new VisaApplicationAdminAlert($application));
        } catch (\Throwable $e) {
            report($e); // never block submission on a mail failure
        }

        return redirect()
            ->route('visa.apply.success', ['country' => $country])
            ->with('reference', $reference);
    }

    /**
     * Confirmation screen with the reference number.
     */
    public function success(string $country)
    {
        $config = \App\Models\VisaCountry::config()[$country] ?? null;
        abort_if(! $config, 404);

        $reference = session('reference');

        // Don't allow visiting the success page directly without a fresh submission.
        if (! $reference) {
            return redirect()->route('visa.show', $country);
        }

        return Inertia::render('Visa/ApplySuccess', [
            'reference' => $reference,
            'country' => ['name' => $config['name'], 'flag' => $config['flag']],
        ]);
    }
}
