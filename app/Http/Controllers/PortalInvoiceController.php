<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Inertia\Inertia;

class PortalInvoiceController extends Controller
{
    /** List the signed-in customer's invoices. */
    public function index()
    {
        $invoices = Invoice::query()
            ->where('user_id', auth()->id())
            ->where('status', '!=', 'draft')
            ->latest()
            ->get()
            ->map(fn ($i) => [
                'id' => $i->id,
                'number' => $i->number,
                'currency' => $i->currency,
                'total' => (float) $i->total,
                'balance' => $i->balance(),
                'status' => $i->status,
                'issue_date' => $i->issue_date?->format('d M Y'),
                'due_date' => $i->due_date?->format('d M Y'),
            ]);

        return Inertia::render('Portal/Invoices', ['invoices' => $invoices]);
    }

    /** Download one of the customer's own invoices. */
    public function pdf(Invoice $invoice)
    {
        abort_unless($invoice->user_id === auth()->id() && $invoice->status !== 'draft', 403);

        return Pdf::loadView('pdf.invoice', ['invoice' => $invoice])
            ->download($invoice->number.'.pdf');
    }
}
