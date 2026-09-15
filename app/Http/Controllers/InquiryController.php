<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    /** Contact page. */
    public function contact()
    {
        return view('contact');
    }

    /** Store a general contact inquiry. */
    public function storeContact(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'subject' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        Lead::create([...$data, 'service' => 'general', 'status' => 'new']);

        return back()->with('success', 'Thanks! Your message has been sent — we’ll get back to you shortly.');
    }

    /** Air ticket request page. */
    public function tickets()
    {
        return view('air-tickets');
    }

    /** Store an air ticket request as a lead. */
    public function storeTicket(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'trip_type' => ['required', 'in:one_way,round_trip'],
            'from' => ['required', 'string', 'max:80'],
            'to' => ['required', 'string', 'max:80'],
            'depart_date' => ['required', 'date', 'after_or_equal:today'],
            'return_date' => ['nullable', 'date', 'after_or_equal:depart_date'],
            'passengers' => ['required', 'integer', 'min:1', 'max:20'],
            'cabin' => ['required', 'string', 'max:30'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        Lead::create([
            'service' => 'ticket',
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'subject' => "Air ticket: {$data['from']} → {$data['to']}",
            'message' => $data['notes'] ?? null,
            'meta' => collect($data)->only(['trip_type', 'from', 'to', 'depart_date', 'return_date', 'passengers', 'cabin'])->toArray(),
            'status' => 'new',
        ]);

        return back()->with('success', 'Your ticket request is in! Our team will send you the best fares shortly.');
    }

    /** Tour packages listing. */
    public function packages()
    {
        return view('packages', ['packages' => \App\Models\Package::activeOrdered()->get()]);
    }

    /** Package booking page. */
    public function bookPackage(\App\Models\Package $package)
    {
        abort_unless($package->active, 404);

        return view('package-book', ['package' => $package]);
    }

    /** Store a package booking request as a lead. */
    public function storePackageBooking(Request $request, \App\Models\Package $package)
    {
        abort_unless($package->active, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'travellers' => ['required', 'integer', 'min:1', 'max:30'],
            'travel_date' => ['nullable', 'date', 'after_or_equal:today'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        Lead::create([
            'service' => 'package',
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'subject' => 'Package booking: '.$package->title,
            'message' => $data['message'] ?? null,
            'meta' => [
                'package' => $package->title,
                'destination' => $package->destination,
                'nights' => $package->nights,
                'price_from' => $package->price,
                'travellers' => $data['travellers'],
                'travel_date' => $data['travel_date'] ?? null,
            ],
            'status' => 'new',
        ]);

        return redirect()
            ->route('packages.book', $package->slug)
            ->with('success', 'Your booking request for '.$package->title.' has been received! Our team will confirm availability and pricing shortly.');
    }
}
