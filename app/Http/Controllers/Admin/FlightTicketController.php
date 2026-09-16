<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FlightTicket;
use App\Models\User;
use App\Models\VisaApplication;
use App\Notifications\FlightTicketIssued;
use App\Services\AiTicketExtractor;
use App\Services\TicketPdfParser;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class FlightTicketController extends Controller
{
    public const STATUSES = ['draft', 'issued', 'cancelled'];

    public function index(Request $request)
    {
        $tickets = FlightTicket::query()
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->search, fn ($q, $s) => $q->where(fn ($q) => $q
                ->where('number', 'like', "%$s%")
                ->orWhere('pnr', 'like', "%$s%")
                ->orWhere('client_name', 'like', "%$s%")
                ->orWhere('client_email', 'like', "%$s%")))
            ->latest()
            ->paginate(12)
            ->withQueryString()
            ->through(fn ($t) => [
                'id' => $t->id,
                'number' => $t->number,
                'pnr' => $t->pnr,
                'client_name' => $t->client_name,
                'airline' => $t->airline,
                'route' => $t->routeSummary(),
                'status' => $t->status,
                'issue_date' => $t->issue_date?->format('d M Y'),
            ]);

        return Inertia::render('Admin/Tickets/Index', [
            'tickets' => $tickets,
            'filters' => $request->only(['status', 'search']),
            'statuses' => self::STATUSES,
            'summary' => [
                'total' => FlightTicket::count(),
                'issued' => FlightTicket::where('status', 'issued')->count(),
                'draft' => FlightTicket::where('status', 'draft')->count(),
            ],
        ]);
    }

    public function create(Request $request)
    {
        $prefill = null;
        if ($request->application && $app = VisaApplication::find($request->application)) {
            $client = User::clients()->where('email', $app->email)->first();
            $prefill = [
                'visa_application_id' => $app->id,
                'user_id' => $client?->id,
                'client_name' => $app->full_name,
                'client_email' => $app->email,
                'client_phone' => $app->phone,
            ];
        }

        return Inertia::render('Admin/Tickets/Form', [
            'ticket' => null,
            'prefill' => $prefill,
            'nextNumber' => FlightTicket::nextNumber(),
            'statuses' => self::STATUSES,
            'clients' => $this->clientOptions(),
        ]);
    }

    public function edit(FlightTicket $flightTicket)
    {
        return Inertia::render('Admin/Tickets/Form', [
            'ticket' => array_merge($flightTicket->toArray(), [
                'issue_date' => $flightTicket->issue_date?->format('Y-m-d'),
            ]),
            'prefill' => null,
            'statuses' => self::STATUSES,
            'clients' => $this->clientOptions(),
        ]);
    }

    /**
     * Best-effort extraction of ticket fields from an uploaded text-based PDF.
     * Pattern parser first (free, for known layouts); if it isn't confident and
     * an OpenAI key is configured, fall back to AI extraction for any layout.
     */
    public function parse(Request $request, TicketPdfParser $parser, AiTicketExtractor $ai)
    {
        $request->validate([
            'source_file' => ['required', 'file', 'mimes:pdf', 'max:8192'],
        ]);

        $path = $request->file('source_file')->getRealPath();
        $result = $parser->parse($path);
        $data = $result['data'] ?? [];

        $confident = ! empty($data['pnr']) && ! empty($data['segments']) && ! empty($data['passengers']);

        if (! $confident && $ai->enabled()) {
            $aiData = $ai->extract($parser->text($path));
            if (! empty($aiData) && (! empty($aiData['pnr']) || ! empty($aiData['segments']))) {
                return response()->json(['ok' => true, 'reason' => null, 'source' => 'ai', 'data' => $aiData]);
            }
        }

        $result['source'] = 'pattern';

        return response()->json($result);
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

        $ticket = new FlightTicket($data);
        $ticket->number = ($data['number'] ?? '') ?: FlightTicket::nextNumber();
        $ticket->save();

        $this->handleUpload($request, $ticket);

        return redirect()->route('admin.tickets.show', $ticket->id)
            ->with('success', 'Ticket '.$ticket->number.' created.');
    }

    public function update(Request $request, FlightTicket $flightTicket)
    {
        $data = $this->validateData($request, $flightTicket->id);
        $flightTicket->fill($data);
        $flightTicket->save();

        $this->handleUpload($request, $flightTicket);

        return redirect()->route('admin.tickets.show', $flightTicket->id)
            ->with('success', 'Ticket updated.');
    }

    public function show(FlightTicket $flightTicket)
    {
        return Inertia::render('Admin/Tickets/Show', [
            'ticket' => array_merge($flightTicket->toArray(), [
                'issue_date' => $flightTicket->issue_date?->format('Y-m-d'),
                'route' => $flightTicket->routeSummary(),
                'has_source' => (bool) $flightTicket->source_file_path,
            ]),
            'statuses' => self::STATUSES,
        ]);
    }

    public function destroy(FlightTicket $flightTicket)
    {
        if ($flightTicket->source_file_path) {
            Storage::disk('local')->delete($flightTicket->source_file_path);
        }
        $flightTicket->delete();

        return redirect()->route('admin.tickets.index')->with('success', 'Ticket deleted.');
    }

    /** Change status (draft / issued / cancelled). */
    public function status(Request $request, FlightTicket $flightTicket)
    {
        $data = $request->validate(['status' => ['required', Rule::in(self::STATUSES)]]);
        $flightTicket->update(['status' => $data['status']]);

        return back()->with('success', 'Ticket marked as '.$data['status'].'.');
    }

    /** Download the branded WindowTrip e-ticket PDF. */
    public function pdf(FlightTicket $flightTicket)
    {
        return Pdf::loadView('pdf.ticket', ['ticket' => $flightTicket])
            ->download($flightTicket->number.'.pdf');
    }

    /** Stream the original uploaded ticket (private). */
    public function source(FlightTicket $flightTicket)
    {
        abort_unless($flightTicket->source_file_path, 404);
        $disk = Storage::disk('local');
        abort_unless($disk->exists($flightTicket->source_file_path), 404);

        return $disk->response($flightTicket->source_file_path);
    }

    /** Email the branded e-ticket (with PDF) to the client. */
    public function email(FlightTicket $flightTicket)
    {
        if (! $flightTicket->client_email) {
            return back()->with('error', 'This ticket has no client email — add one first to send it.');
        }

        try {
            Notification::route('mail', $flightTicket->client_email)
                ->notify(new FlightTicketIssued($flightTicket));
            if ($flightTicket->status === 'draft') {
                $flightTicket->update(['status' => 'issued']);
            }
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Could not send the ticket email — check mail settings.');
        }

        return back()->with('success', 'E-ticket emailed to '.$flightTicket->client_email.'.');
    }

    /** Store the uploaded original ticket file, replacing any previous one. */
    private function handleUpload(Request $request, FlightTicket $ticket): void
    {
        if (! $request->hasFile('source_file')) {
            return;
        }

        if ($ticket->source_file_path) {
            Storage::disk('local')->delete($ticket->source_file_path);
        }

        $file = $request->file('source_file');
        $ticket->source_file_path = $file->store("tickets/{$ticket->number}", 'local');
        $ticket->source_file_name = $file->getClientOriginalName();
        $ticket->save();
    }

    private function validateData(Request $request, ?int $ignore = null): array
    {
        return $request->validate([
            'number' => ['nullable', 'string', 'max:40', Rule::unique('flight_tickets', 'number')->ignore($ignore)],
            'visa_application_id' => ['nullable', 'exists:visa_applications,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'client_name' => ['required', 'string', 'max:120'],
            'client_email' => ['nullable', 'email', 'max:120'],
            'client_phone' => ['nullable', 'string', 'max:40'],
            'pnr' => ['required', 'string', 'max:20'],
            'booking_ref' => ['nullable', 'string', 'max:40'],
            'airline' => ['nullable', 'string', 'max:80'],
            'currency' => ['required', 'string', 'max:8'],
            'fare_total' => ['nullable', 'numeric', 'min:0'],
            'issue_date' => ['required', 'date'],
            'status' => ['required', Rule::in(self::STATUSES)],
            'notes' => ['nullable', 'string', 'max:2000'],
            'source_file' => ['nullable', 'file', 'mimes:pdf,png,jpg,jpeg', 'max:8192'],

            'passengers' => ['required', 'array', 'min:1'],
            'passengers.*.name' => ['required', 'string', 'max:120'],
            'passengers.*.type' => ['nullable', 'string', 'max:20'],
            'passengers.*.ticket_number' => ['nullable', 'string', 'max:40'],
            'passengers.*.seat' => ['nullable', 'string', 'max:20'],

            'segments' => ['required', 'array', 'min:1'],
            'segments.*.airline' => ['nullable', 'string', 'max:80'],
            'segments.*.flight_number' => ['required', 'string', 'max:20'],
            'segments.*.cabin' => ['nullable', 'string', 'max:40'],
            'segments.*.from_code' => ['required', 'string', 'max:8'],
            'segments.*.from_city' => ['nullable', 'string', 'max:80'],
            'segments.*.to_code' => ['required', 'string', 'max:8'],
            'segments.*.to_city' => ['nullable', 'string', 'max:80'],
            'segments.*.depart_at' => ['required', 'string', 'max:40'],
            'segments.*.arrive_at' => ['nullable', 'string', 'max:40'],
            'segments.*.baggage' => ['nullable', 'string', 'max:40'],
        ]);
    }
}
