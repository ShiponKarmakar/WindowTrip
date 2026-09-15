<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VisaApplication;
use App\Models\VisaCountry;
use App\Notifications\ApplicationMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Throwable;

class ApplicationController extends Controller
{
    public const STATUSES = ['submitted', 'under_review', 'docs_required', 'approved', 'rejected'];

    public function index(Request $request)
    {
        $applications = VisaApplication::query()
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->country, fn ($q, $c) => $q->where('country', $c))
            ->when($request->search, fn ($q, $s) => $q->where(function ($q) use ($s) {
                $q->where('full_name', 'like', "%$s%")
                    ->orWhere('reference', 'like', "%$s%")
                    ->orWhere('email', 'like', "%$s%");
            }))
            ->latest()
            ->paginate(12)
            ->withQueryString()
            ->through(fn ($a) => [
                'id' => $a->id,
                'reference' => $a->reference,
                'name' => $a->full_name,
                'email' => $a->email,
                'country' => $a->countryName(),
                'flag' => $a->countryFlag(),
                'visa_type' => $a->visa_type,
                'status' => $a->status,
                'created' => $a->created_at->format('d M Y'),
            ]);

        return Inertia::render('Admin/Applications/Index', [
            'applications' => $applications,
            'filters' => $request->only(['status', 'country', 'search']),
            'statuses' => self::STATUSES,
            'countries' => collect(VisaCountry::config())->map(fn ($v, $slug) => ['slug' => $slug, 'name' => $v['name']])->values(),
        ]);
    }

    public function show(VisaApplication $application)
    {
        return Inertia::render('Admin/Applications/Show', [
            'application' => [
                'id' => $application->id,
                'reference' => $application->reference,
                'status' => $application->status,
                'country' => $application->countryName(),
                'flag' => $application->countryFlag(),
                'visa_type' => $application->visa_type,
                'travellers' => $application->travellers,
                'travel_date' => $application->travel_date?->format('d M Y'),
                'full_name' => $application->full_name,
                'date_of_birth' => $application->date_of_birth?->format('d M Y'),
                'gender' => $application->gender,
                'nationality' => $application->nationality,
                'passport_number' => $application->passport_number,
                'passport_expiry' => $application->passport_expiry?->format('d M Y'),
                'email' => $application->email,
                'phone' => $application->phone,
                'notes' => $application->notes,
                'has_passport_scan' => (bool) $application->passport_scan_path,
                'has_photo' => (bool) $application->photo_path,
                'created' => $application->created_at->format('d M Y, g:i a'),
            ],
            'statuses' => self::STATUSES,
        ]);
    }

    public function updateStatus(Request $request, VisaApplication $application)
    {
        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', self::STATUSES)],
        ]);

        $application->update(['status' => $data['status']]);

        return back()->with('success', 'Status updated to '.str_replace('_', ' ', $data['status']).'.');
    }

    /** Edit a submitted application's details. */
    public function edit(VisaApplication $application)
    {
        return Inertia::render('Admin/Applications/Edit', [
            'application' => [
                'id' => $application->id,
                'reference' => $application->reference,
                'country' => $application->countryName(),
                'flag' => $application->countryFlag(),
                'visa_type' => $application->visa_type,
                'travellers' => $application->travellers,
                'travel_date' => $application->travel_date?->toDateString(),
                'full_name' => $application->full_name,
                'date_of_birth' => $application->date_of_birth?->toDateString(),
                'gender' => $application->gender,
                'nationality' => $application->nationality,
                'passport_number' => $application->passport_number,
                'passport_expiry' => $application->passport_expiry?->toDateString(),
                'email' => $application->email,
                'phone' => $application->phone,
                'notes' => $application->notes,
            ],
            'statuses' => self::STATUSES,
        ]);
    }

    public function update(Request $request, VisaApplication $application)
    {
        $data = $request->validate([
            'visa_type' => ['required', 'string', 'max:50'],
            'travellers' => ['required', 'integer', 'min:1', 'max:20'],
            'travel_date' => ['nullable', 'date'],
            'full_name' => ['required', 'string', 'max:120'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'string', 'max:20'],
            'nationality' => ['required', 'string', 'max:60'],
            'passport_number' => ['nullable', 'string', 'max:30'],
            'passport_expiry' => ['nullable', 'date'],
            'email' => ['required', 'email', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $application->update($data);

        return redirect()->route('admin.applications.show', $application->id)
            ->with('success', 'Application updated.');
    }

    /** Dedicated compose-email page. */
    public function compose(VisaApplication $application)
    {
        return Inertia::render('Admin/Applications/Compose', [
            'application' => [
                'id' => $application->id,
                'reference' => $application->reference,
                'country' => $application->countryName(),
                'full_name' => $application->full_name,
                'email' => $application->email,
            ],
        ]);
    }

    /** Send an email message to the applicant. */
    public function message(Request $request, VisaApplication $application)
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:150'],
            'body' => ['required', 'string', 'max:50000'],
        ]);

        try {
            Notification::route('mail', $application->email)
                ->notify(new ApplicationMessage(
                $application,
                $data['subject'],
                $data['body']
        ));
        } catch (Throwable $e) {
            report($e);

            return back()->with('success', 'Could not send the email — please check mail settings.');
        }

        return back()->with('success', 'Message sent to '.$application->email.'.');
    }

    /**
     * Stream a privately-stored document (passport scan / photo).
     */
    public function document(VisaApplication $application, string $type)
    {
        $path = $type === 'photo' ? $application->photo_path : $application->passport_scan_path;
        abort_if(! $path, 404);

        $disk = Storage::disk('local');
        abort_unless($disk->exists($path), 404);

        // Inline view; respects the disk's (private) root regardless of Laravel version.
        return $disk->response($path);
    }
}
