<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Setting;
use App\Models\User;
use App\Models\VisaApplication;
use App\Notifications\InvoiceIssued;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    public const STATUSES = ['draft', 'sent', 'partial', 'paid', 'cancelled'];

    public function index(Request $request)
    {
        $invoices = Invoice::query()
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->search, fn ($q, $s) => $q->where(fn ($q) => $q
                ->where('number', 'like', "%$s%")
                ->orWhere('client_name', 'like', "%$s%")
                ->orWhere('client_email', 'like', "%$s%")))
            ->latest()
            ->paginate(12)
            ->withQueryString()
            ->through(fn ($i) => [
                'id' => $i->id,
                'number' => $i->number,
                'client_name' => $i->client_name,
                'currency' => $i->currency,
                'total' => (float) $i->total,
                'balance' => $i->balance(),
                'status' => $i->status,
                'issue_date' => $i->issue_date?->format('d M Y'),
                'due_date' => $i->due_date?->format('d M Y'),
            ]);

        // KPI summary
        $summary = [
            'outstanding' => (float) Invoice::whereNotIn('status', ['paid', 'cancelled'])->sum('total')
                - (float) Invoice::whereNotIn('status', ['paid', 'cancelled'])->sum('amount_paid'),
            'paid' => (float) Invoice::sum('amount_paid'),
            'count' => Invoice::count(),
        ];

        return Inertia::render('Admin/Invoices/Index', [
            'invoices' => $invoices,
            'filters' => $request->only(['status', 'search']),
            'statuses' => self::STATUSES,
            'summary' => $summary,
        ]);
    }

    public function create(Request $request)
    {
        $prefill = null;
        if ($request->application && $app = VisaApplication::find($request->application)) {
            // Link to an existing client account if one matches the applicant's email.
            $client = User::clients()->where('email', $app->email)->first();
            $prefill = [
                'visa_application_id' => $app->id,
                'user_id' => $client?->id,
                'client_name' => $app->full_name,
                'client_email' => $app->email,
                'client_phone' => $app->phone,
                'reference' => $app->reference,
                'item' => $app->countryName().' '.ucfirst($app->visa_type).' visa processing',
            ];
        }

        return Inertia::render('Admin/Invoices/Form', [
            'invoice' => null,
            'prefill' => $prefill,
            'currency' => Setting::get('company_name') ? 'BDT' : 'BDT',
            'nextNumber' => Invoice::nextNumber(),
            'clients' => $this->clientOptions(),
        ]);
    }

    public function edit(Invoice $invoice)
    {
        return Inertia::render('Admin/Invoices/Form', [
            'invoice' => $invoice->append([])->toArray(),
            'prefill' => null,
            'clients' => $this->clientOptions(),
        ]);
    }

    /** Lightweight client list for the picker. */
    private function clientOptions()
    {
        return User::clients()
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'phone'])
            ->map(fn ($u) => ['id' => $u->id, 'name' => $u->name, 'email' => $u->email, 'phone' => $u->phone]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $invoice = new Invoice($data);
        $invoice->number = ($data['number'] ?? '') ?: Invoice::nextNumber();
        $invoice->amount_paid = 0;
        $invoice->recalculate();
        $invoice->save();

        $this->handleVoucher($request, $invoice);

        return redirect()->route('admin.invoices.show', $invoice->id)->with('success', 'Invoice '.$invoice->number.' created.');
    }

    public function update(Request $request, Invoice $invoice)
    {
        $data = $this->validateData($request, $invoice->id);
        $invoice->fill($data);
        $invoice->recalculate();
        $invoice->save();

        $this->handleVoucher($request, $invoice);

        return redirect()->route('admin.invoices.show', $invoice->id)->with('success', 'Invoice updated.');
    }

    /** Store the optional uploaded voucher, replacing any previous one. */
    private function handleVoucher(Request $request, Invoice $invoice): void
    {
        $request->validate([
            'voucher' => ['nullable', 'file', 'mimes:pdf,png,jpg,jpeg', 'max:8192'],
        ]);

        if (! $request->hasFile('voucher')) {
            return;
        }

        if ($invoice->voucher_path) {
            Storage::disk('local')->delete($invoice->voucher_path);
        }

        $file = $request->file('voucher');
        $invoice->voucher_path = $file->store("invoices/{$invoice->number}", 'local');
        $invoice->voucher_name = $file->getClientOriginalName();
        $invoice->save();
    }

    /** Stream the privately-stored voucher. */
    public function voucher(Invoice $invoice)
    {
        abort_unless($invoice->voucher_path, 404);
        $disk = Storage::disk('local');
        abort_unless($disk->exists($invoice->voucher_path), 404);

        return $disk->response($invoice->voucher_path);
    }

    public function show(Invoice $invoice)
    {
        return Inertia::render('Admin/Invoices/Show', [
            'invoice' => array_merge($invoice->toArray(), [
                'balance' => $invoice->balance(),
                'issue_date' => $invoice->issue_date?->format('Y-m-d'),
                'due_date' => $invoice->due_date?->format('Y-m-d'),
                'has_voucher' => (bool) $invoice->voucher_path,
            ]),
            'statuses' => self::STATUSES,
        ]);
    }

    public function destroy(Invoice $invoice)
    {
        if ($invoice->voucher_path) {
            Storage::disk('local')->delete($invoice->voucher_path);
        }
        $invoice->delete();

        return redirect()->route('admin.invoices.index')->with('success', 'Invoice deleted.');
    }

    /** Record a manual payment. */
    public function payment(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'date' => ['required', 'date'],
            'method' => ['nullable', 'string', 'max:40'],
            'note' => ['nullable', 'string', 'max:200'],
        ]);

        $payments = $invoice->payments ?? [];
        $payments[] = [
            'date' => $data['date'],
            'amount' => round((float) $data['amount'], 2),
            'method' => $data['method'] ?? 'Manual',
            'note' => $data['note'] ?? null,
        ];
        $invoice->payments = $payments;
        $invoice->amount_paid = round((float) $invoice->amount_paid + (float) $data['amount'], 2);
        $invoice->recalculate();
        $invoice->save();

        return back()->with('success', 'Payment of '.$invoice->currency.' '.number_format((float) $data['amount'], 2).' recorded.');
    }

    /** Change status (sent / cancelled / draft). */
    public function status(Request $request, Invoice $invoice)
    {
        $data = $request->validate(['status' => ['required', 'in:draft,sent,cancelled']]);
        $invoice->status = $data['status'];
        $invoice->recalculate();
        $invoice->save();

        return back()->with('success', 'Invoice marked as '.$data['status'].'.');
    }

    /** Download the invoice as a PDF. */
    public function pdf(Invoice $invoice)
    {
        return Pdf::loadView('pdf.invoice', ['invoice' => $invoice])
            ->download($invoice->number.'.pdf');
    }

    /** Email the invoice (with PDF) to the client. */
    public function email(Invoice $invoice)
    {
        try {
            Notification::route('mail', $invoice->client_email)->notify(new InvoiceIssued($invoice));
            if ($invoice->status === 'draft') {
                $invoice->update(['status' => 'sent']);
            }
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Could not send the invoice email — check mail settings.');
        }

        return back()->with('success', 'Invoice emailed to '.$invoice->client_email.'.');
    }

    private function validateData(Request $request, ?int $ignore = null): array
    {
        return $request->validate([
            'number' => ['nullable', 'string', 'max:40', \Illuminate\Validation\Rule::unique('invoices', 'number')->ignore($ignore)],
            'visa_application_id' => ['nullable', 'exists:visa_applications,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'client_name' => ['required', 'string', 'max:120'],
            'client_email' => ['required', 'email', 'max:120'],
            'client_phone' => ['nullable', 'string', 'max:40'],
            'currency' => ['required', 'string', 'max:8'],
            'issue_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:issue_date'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:'.implode(',', self::STATUSES)],
            'notes' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:200'],
            'items.*.qty' => ['required', 'numeric', 'min:0'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);
    }
}
