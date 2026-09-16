@php
    $company = \App\Models\Setting::get('company_name');
    $email = \App\Models\Setting::get('support_email');
    $phone = \App\Models\Setting::get('support_phone');
    $address = \App\Models\Setting::get('office_address');
    $primary = '#139dd5';
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { color: #1f2937; font-size: 12px; margin: 0; }
        .wrap { padding: 36px 40px; }
        .row { width: 100%; }
        .head td { vertical-align: top; }
        h1 { color: {{ $primary }}; margin: 0 0 2px; font-size: 26px; }
        .muted { color: #6b7280; }
        .brand { font-size: 20px; font-weight: bold; color: #12263b; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 22px; }
        table.items th { background: {{ $primary }}; color: #fff; text-align: left; padding: 8px 10px; font-size: 11px; }
        table.items td { padding: 8px 10px; border-bottom: 1px solid #eef2f7; }
        .right { text-align: right; }
        .totals { width: 45%; margin-left: 55%; margin-top: 16px; }
        .totals td { padding: 5px 10px; }
        .totals .grand { font-size: 15px; font-weight: bold; color: {{ $primary }}; border-top: 2px solid #eef2f7; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: bold; }
        .footer { margin-top: 34px; color: #6b7280; font-size: 11px; border-top: 1px solid #eef2f7; padding-top: 12px; }
    </style>
</head>
<body>
<div class="wrap">
    <table class="row head">
        <tr>
            <td>
                <div class="brand">{{ $company }}</div>
                <div class="muted">{{ \App\Models\Setting::get('tagline') }}</div>
                <div class="muted" style="margin-top:8px">{{ $address }}<br>{{ $email }} · {{ $phone }}</div>
            </td>
            <td class="right">
                <h1>INVOICE</h1>
                <div><strong>{{ $invoice->number }}</strong></div>
                <div class="muted">Issued: {{ $invoice->issue_date?->format('d M Y') }}</div>
                @if($invoice->due_date)<div class="muted">Due: {{ $invoice->due_date->format('d M Y') }}</div>@endif
                <div style="margin-top:6px">
                    @php $st = $invoice->status; @endphp
                    <span class="badge" style="background: {{ $st==='paid' ? '#dcfce7' : ($st==='cancelled' ? '#fee2e2' : '#e8f6fc') }}; color: {{ $st==='paid' ? '#166534' : ($st==='cancelled' ? '#b91c1c' : '#0f7fae') }}">{{ strtoupper(str_replace('_',' ',$st)) }}</span>
                </div>
            </td>
        </tr>
    </table>

    <div style="margin-top:24px">
        <div class="muted" style="text-transform:uppercase; font-size:10px; letter-spacing:1px">Bill to</div>
        <div style="font-weight:bold; margin-top:2px">{{ $invoice->client_name }}</div>
        <div class="muted">{{ $invoice->client_email }}@if($invoice->client_phone) · {{ $invoice->client_phone }}@endif</div>
        @if($invoice->application)<div class="muted">Ref: {{ $invoice->application->reference }} ({{ $invoice->application->countryName() }})</div>@endif
    </div>

    <table class="items">
        <thead>
            <tr><th style="width:55%">Description</th><th class="right">Qty</th><th class="right">Unit price</th><th class="right">Amount</th></tr>
        </thead>
        <tbody>
            @foreach($invoice->items ?? [] as $it)
                <tr>
                    <td>{{ $it['description'] ?? '' }}</td>
                    <td class="right">{{ $it['qty'] ?? 0 }}</td>
                    <td class="right">{{ number_format((float)($it['unit_price'] ?? 0), 2) }}</td>
                    <td class="right">{{ number_format((float)($it['qty'] ?? 0) * (float)($it['unit_price'] ?? 0), 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr><td class="muted">Subtotal</td><td class="right">{{ $invoice->currency }} {{ number_format((float)$invoice->subtotal, 2) }}</td></tr>
        @if((float)$invoice->discount > 0)<tr><td class="muted">Discount</td><td class="right">− {{ number_format((float)$invoice->discount, 2) }}</td></tr>@endif
        @if((float)$invoice->tax > 0)<tr><td class="muted">Tax</td><td class="right">{{ number_format((float)$invoice->tax, 2) }}</td></tr>@endif
        <tr class="grand"><td>Total</td><td class="right">{{ $invoice->currency }} {{ number_format((float)$invoice->total, 2) }}</td></tr>
        @if((float)$invoice->amount_paid > 0)
            <tr><td class="muted">Paid</td><td class="right">− {{ number_format((float)$invoice->amount_paid, 2) }}</td></tr>
            <tr class="grand"><td>Balance due</td><td class="right">{{ $invoice->currency }} {{ number_format($invoice->balance(), 2) }}</td></tr>
        @endif
    </table>

    @if($invoice->notes)
        <div style="margin-top:26px"><div class="muted" style="text-transform:uppercase; font-size:10px; letter-spacing:1px">Notes</div><div style="margin-top:4px">{{ $invoice->notes }}</div></div>
    @endif

    <div class="footer">
        {{ $company }} — visa processing, air tickets & tour packages. Government/embassy fees are billed separately. Thank you for your business.
    </div>
</div>
</body>
</html>
