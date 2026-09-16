<?php

namespace App\Http\Controllers;

use App\Models\FlightTicket;
use Barryvdh\DomPDF\Facade\Pdf;
use Inertia\Inertia;

class PortalTicketController extends Controller
{
    /** List the signed-in customer's issued e-tickets. */
    public function index()
    {
        $tickets = FlightTicket::query()
            ->where('user_id', auth()->id())
            ->where('status', '!=', 'draft')
            ->latest()
            ->get()
            ->map(fn ($t) => [
                'id' => $t->id,
                'number' => $t->number,
                'pnr' => $t->pnr,
                'airline' => $t->airline,
                'route' => $t->routeSummary(),
                'status' => $t->status,
                'issue_date' => $t->issue_date?->format('d M Y'),
            ]);

        return Inertia::render('Portal/Tickets', ['tickets' => $tickets]);
    }

    /** Download one of the customer's own e-tickets. */
    public function pdf(FlightTicket $flightTicket)
    {
        abort_unless($flightTicket->user_id === auth()->id() && $flightTicket->status !== 'draft', 403);

        return Pdf::loadView('pdf.ticket', ['ticket' => $flightTicket])
            ->download($flightTicket->number.'.pdf');
    }
}
