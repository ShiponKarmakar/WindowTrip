<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LeadController extends Controller
{
    public const STATUSES = ['new', 'contacted', 'converted', 'closed'];

    public function index(Request $request)
    {
        $leads = Lead::query()
            ->when($request->service, fn ($q, $s) => $q->where('service', $s))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(15)
            ->withQueryString()
            ->through(fn ($l) => [
                'id' => $l->id,
                'service' => $l->service,
                'name' => $l->name,
                'email' => $l->email,
                'phone' => $l->phone,
                'subject' => $l->subject,
                'message' => $l->message,
                'meta' => $l->meta,
                'status' => $l->status,
                'created' => $l->created_at->format('d M Y'),
            ]);

        return Inertia::render('Admin/Leads/Index', [
            'leads' => $leads,
            'filters' => $request->only(['service', 'status']),
            'statuses' => self::STATUSES,
        ]);
    }

    public function updateStatus(Request $request, Lead $lead)
    {
        $data = $request->validate(['status' => ['required', 'in:'.implode(',', self::STATUSES)]]);
        $lead->update($data);

        return back()->with('success', 'Lead marked as '.$data['status'].'.');
    }
}
