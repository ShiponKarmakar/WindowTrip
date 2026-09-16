<x-mail::message>
# Invoice {{ $invoice->number }}

Hello {{ $invoice->client_name }},

Please find your invoice from **{{ \App\Models\Setting::get('company_name') }}** attached as a PDF.

<x-mail::panel>
**Invoice:** {{ $invoice->number }}
**Issued:** {{ $invoice->issue_date?->format('d M Y') }}
@if($invoice->due_date)
**Due:** {{ $invoice->due_date->format('d M Y') }}
@endif
**Total:** {{ $invoice->currency }} {{ number_format((float) $invoice->total, 2) }}
@if($invoice->balance() > 0)
**Balance due:** {{ $invoice->currency }} {{ number_format($invoice->balance(), 2) }}
@endif
</x-mail::panel>

If you have any questions about this invoice, just reply to this email.

Thank you,<br>
**{{ \App\Models\Setting::get('company_name') }}** — {{ \App\Models\Setting::get('tagline') }}
</x-mail::message>
