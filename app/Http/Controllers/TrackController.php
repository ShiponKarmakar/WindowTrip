<?php

namespace App\Http\Controllers;

use App\Models\VisaApplication;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TrackController extends Controller
{
    /** Show the track-application form. */
    public function show()
    {
        return Inertia::render('Public/Track', ['result' => null, 'searched' => false]);
    }

    /** Look up an application by reference + email. */
    public function check(Request $request)
    {
        $data = $request->validate([
            'reference' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:120'],
        ]);

        $application = VisaApplication::query()
            ->whereRaw('UPPER(reference) = ?', [strtoupper(trim($data['reference']))])
            ->whereRaw('LOWER(email) = ?', [strtolower(trim($data['email']))])
            ->first();

        return Inertia::render('Public/Track', [
            'searched' => true,
            'old' => ['reference' => $data['reference'], 'email' => $data['email']],
            'result' => $application ? [
                'reference' => $application->reference,
                'name' => $application->full_name,
                'country' => $application->countryName(),
                'flag' => $application->countryFlag(),
                'visa_type' => $application->visa_type,
                'status' => $application->status,
                'submitted' => $application->created_at->format('d M Y'),
                'updated' => $application->updated_at->format('d M Y'),
            ] : null,
        ]);
    }
}
