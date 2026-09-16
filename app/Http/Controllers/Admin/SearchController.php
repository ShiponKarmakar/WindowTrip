<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FlightTicket;
use App\Models\Invoice;
use App\Models\User;
use App\Models\VisaApplication;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /** Global admin search across tickets, invoices, clients and applications. */
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json(['groups' => []]);
        }

        $like = '%'.$q.'%';
        $groups = [];

        $tickets = FlightTicket::query()
            ->where(fn ($w) => $w->where('number', 'like', $like)
                ->orWhere('pnr', 'like', $like)
                ->orWhere('client_name', 'like', $like))
            ->latest()->limit(5)->get();
        if ($tickets->isNotEmpty()) {
            $groups[] = [
                'label' => 'Flight tickets',
                'items' => $tickets->map(fn ($t) => [
                    'title' => $t->number,
                    'sub' => trim(($t->client_name ?? '').' · '.($t->pnr ?? '')),
                    'url' => route('admin.tickets.show', $t->id),
                ]),
            ];
        }

        $invoices = Invoice::query()
            ->where(fn ($w) => $w->where('number', 'like', $like)
                ->orWhere('client_name', 'like', $like)
                ->orWhere('client_email', 'like', $like))
            ->latest()->limit(5)->get();
        if ($invoices->isNotEmpty()) {
            $groups[] = [
                'label' => 'Invoices',
                'items' => $invoices->map(fn ($i) => [
                    'title' => $i->number,
                    'sub' => trim(($i->client_name ?? '').' · '.$i->currency.' '.number_format((float) $i->total, 2)),
                    'url' => route('admin.invoices.show', $i->id),
                ]),
            ];
        }

        $clients = User::clients()
            ->where(fn ($w) => $w->where('name', 'like', $like)
                ->orWhere('email', 'like', $like)
                ->orWhere('phone', 'like', $like))
            ->limit(5)->get();
        if ($clients->isNotEmpty()) {
            $groups[] = [
                'label' => 'Clients',
                'items' => $clients->map(fn ($c) => [
                    'title' => $c->name,
                    'sub' => $c->email,
                    'url' => route('admin.clients.show', $c->id),
                ]),
            ];
        }

        $apps = VisaApplication::query()
            ->where(fn ($w) => $w->where('reference', 'like', $like)
                ->orWhere('full_name', 'like', $like)
                ->orWhere('email', 'like', $like))
            ->latest()->limit(5)->get();
        if ($apps->isNotEmpty()) {
            $groups[] = [
                'label' => 'Visa applications',
                'items' => $apps->map(fn ($a) => [
                    'title' => $a->reference,
                    'sub' => trim(($a->full_name ?? '').' · '.ucfirst((string) $a->visa_type)),
                    'url' => route('admin.applications.show', $a->id),
                ]),
            ];
        }

        return response()->json(['groups' => $groups]);
    }
}
