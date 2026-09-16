<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $clients = User::clients()
            ->when($request->search, fn ($q, $s) => $q->where(fn ($q) => $q
                ->where('name', 'like', "%$s%")
                ->orWhere('email', 'like', "%$s%")
                ->orWhere('phone', 'like', "%$s%")))
            ->withCount(['invoices', 'flightTickets'])
            ->latest()
            ->paginate(15)
            ->withQueryString()
            ->through(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'phone' => $u->phone,
                'invoices_count' => $u->invoices_count,
                'tickets_count' => $u->flight_tickets_count,
                'created' => $u->created_at?->format('d M Y'),
            ]);

        return Inertia::render('Admin/Clients/Index', [
            'clients' => $clients,
            'filters' => $request->only(['search']),
            'summary' => ['total' => User::clients()->count()],
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Clients/Form', ['client' => null]);
    }

    public function edit(User $client)
    {
        abort_if($client->isStaff(), 404);

        return Inertia::render('Admin/Clients/Form', [
            'client' => [
                'id' => $client->id,
                'name' => $client->name,
                'email' => $client->email,
                'phone' => $client->phone,
                'address' => $client->address,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $client = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            // Random password — the client sets their own via "forgot password".
            'password' => Hash::make(Str::random(32)),
        ]);

        return redirect()->route('admin.clients.show', $client->id)
            ->with('success', 'Client '.$client->name.' added.');
    }

    public function update(Request $request, User $client)
    {
        abort_if($client->isStaff(), 404);
        $data = $this->validateData($request, $client->id);

        $client->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
        ]);

        return redirect()->route('admin.clients.show', $client->id)
            ->with('success', 'Client updated.');
    }

    public function show(User $client)
    {
        abort_if($client->isStaff(), 404);

        return Inertia::render('Admin/Clients/Show', [
            'client' => [
                'id' => $client->id,
                'name' => $client->name,
                'email' => $client->email,
                'phone' => $client->phone,
                'address' => $client->address,
                'created' => $client->created_at?->format('d M Y'),
            ],
            'invoices' => $client->invoices()->latest()->get()->map(fn ($i) => [
                'id' => $i->id,
                'number' => $i->number,
                'currency' => $i->currency,
                'total' => (float) $i->total,
                'balance' => $i->balance(),
                'status' => $i->status,
                'issue_date' => $i->issue_date?->format('d M Y'),
            ]),
            'tickets' => $client->flightTickets()->latest()->get()->map(fn ($t) => [
                'id' => $t->id,
                'number' => $t->number,
                'pnr' => $t->pnr,
                'route' => $t->routeSummary(),
                'status' => $t->status,
                'issue_date' => $t->issue_date?->format('d M Y'),
            ]),
        ]);
    }

    public function destroy(User $client)
    {
        abort_if($client->isStaff(), 404);
        // Invoices/tickets keep their client_name snapshot; user_id is nulled by the FK.
        $client->delete();

        return redirect()->route('admin.clients.index')->with('success', 'Client deleted.');
    }

    private function validateData(Request $request, ?int $ignore = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120', Rule::unique('users', 'email')->ignore($ignore)],
            'phone' => ['nullable', 'string', 'max:40'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
