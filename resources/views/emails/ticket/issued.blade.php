<x-mail::message>
# Your e-ticket {{ $ticket->number }}

Hello {{ $ticket->client_name }},

Your flight e-ticket from **{{ \App\Models\Setting::get('company_name') }}** is ready and attached as a PDF.

<x-mail::panel>
**Ticket:** {{ $ticket->number }}
**Booking PNR:** {{ strtoupper($ticket->pnr) }}
@if($ticket->airline)
**Airline:** {{ $ticket->airline }}
@endif
**Route:** {{ $ticket->routeSummary() }}
**Issued:** {{ $ticket->issue_date?->format('d M Y') }}
</x-mail::panel>

Please carry a valid passport and arrive at the airport at least 3 hours before international departures. Baggage and check-in rules are set by the operating airline.

If you have any questions, just reply to this email.

Thank you,<br>
**{{ \App\Models\Setting::get('company_name') }}** — {{ \App\Models\Setting::get('tagline') }}
</x-mail::message>
