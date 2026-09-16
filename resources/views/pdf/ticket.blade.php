@php
    $company = \App\Models\Setting::get('company_name');
    $email = \App\Models\Setting::get('support_email');
    $phone = \App\Models\Setting::get('support_phone');
    $address = \App\Models\Setting::get('office_address');
    $primary = '#139dd5';
    $logo = public_path('brand/logo-horizontal.png');
    $hasLogo = is_file($logo);
    $fmt = function ($v) {
        if (! $v) return ['date' => '—', 'time' => ''];
        try { $d = \Illuminate\Support\Carbon::parse($v); return ['date' => $d->format('d M Y'), 'time' => $d->format('H:i')]; }
        catch (\Throwable $e) { return ['date' => $v, 'time' => '']; }
    };
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { color: #1f2937; font-size: 12px; margin: 0; }
        .wrap { padding: 34px 40px; }
        .row { width: 100%; border-collapse: collapse; }
        .head td { vertical-align: top; }
        h1 { color: {{ $primary }}; margin: 0 0 2px; font-size: 24px; letter-spacing: 1px; }
        .muted { color: #6b7280; }
        .brand { font-size: 20px; font-weight: bold; color: #12263b; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: bold; }
        .pnrbox { background: {{ $primary }}; color: #fff; border-radius: 10px; padding: 10px 16px; }
        .pnrbox .label { font-size: 9px; letter-spacing: 1px; opacity: .85; }
        .pnrbox .val { font-size: 20px; font-weight: bold; letter-spacing: 2px; }
        .section-label { text-transform: uppercase; font-size: 10px; letter-spacing: 1px; color: #9ca3af; margin-bottom: 4px; }
        .seg { border: 1px solid #e5eef4; border-radius: 12px; margin-top: 12px; }
        .seg .top { background: #f4fafd; padding: 8px 14px; border-bottom: 1px solid #e5eef4; }
        .seg .top .fn { font-weight: bold; color: #12263b; }
        .seg .body { padding: 14px; }
        .seg .body td { vertical-align: top; }
        .airport { font-size: 22px; font-weight: bold; color: #12263b; }
        .city { color: #6b7280; font-size: 11px; }
        .when { font-size: 13px; font-weight: bold; }
        .arrow { color: {{ $primary }}; font-size: 18px; text-align: center; }
        table.pax { width: 100%; border-collapse: collapse; margin-top: 8px; }
        table.pax th { background: {{ $primary }}; color: #fff; text-align: left; padding: 7px 10px; font-size: 10px; }
        table.pax td { padding: 7px 10px; border-bottom: 1px solid #eef2f7; }
        .footer { margin-top: 30px; color: #6b7280; font-size: 10px; border-top: 1px solid #eef2f7; padding-top: 12px; }
    </style>
</head>
<body>
<div class="wrap">
    <table class="row head">
        <tr>
            <td style="width:60%">
                @if($hasLogo)
                    <img src="{{ $logo }}" alt="{{ $company }}" style="width:200px; height:auto; margin-bottom:6px">
                @else
                    <div class="brand">{{ $company }}</div>
                @endif
                <div class="muted">{{ \App\Models\Setting::get('tagline') }}</div>
                <div class="muted" style="margin-top:8px">{{ $address }}<br>{{ $email }} · {{ $phone }}</div>
            </td>
            <td class="right" style="text-align:right">
                <h1>E-TICKET</h1>
                <div><strong>{{ $ticket->number }}</strong></div>
                <div class="muted">Issued: {{ $ticket->issue_date?->format('d M Y') }}</div>
                <div style="margin-top:6px">
                    @php $st = $ticket->status; @endphp
                    <span class="badge" style="background: {{ $st==='issued' ? '#dcfce7' : ($st==='cancelled' ? '#fee2e2' : '#e8f6fc') }}; color: {{ $st==='issued' ? '#166534' : ($st==='cancelled' ? '#b91c1c' : '#0f7fae') }}">{{ strtoupper($st) }}</span>
                </div>
            </td>
        </tr>
    </table>

    <table class="row" style="margin-top:22px">
        <tr>
            <td style="width:65%; vertical-align:middle">
                <div class="section-label">Passenger contact</div>
                <div style="font-weight:bold">{{ $ticket->client_name }}</div>
                <div class="muted">{{ $ticket->client_email }}@if($ticket->client_phone) · {{ $ticket->client_phone }}@endif</div>
                @if($ticket->airline)<div class="muted" style="margin-top:4px">Airline: {{ $ticket->airline }}</div>@endif
            </td>
            <td style="width:35%; text-align:right; vertical-align:middle">
                <table style="margin-left:auto"><tr>
                    <td class="pnrbox">
                        <div class="label">BOOKING PNR</div>
                        <div class="val">{{ strtoupper($ticket->pnr) }}</div>
                    </td>
                </tr></table>
                @if($ticket->booking_ref)<div class="muted" style="margin-top:6px">Airline ref: {{ strtoupper($ticket->booking_ref) }}</div>@endif
            </td>
        </tr>
    </table>

    <div style="margin-top:22px" class="section-label">Itinerary</div>
    @foreach($ticket->segments ?? [] as $s)
        @php $dep = $fmt($s['depart_at'] ?? null); $arr = $fmt($s['arrive_at'] ?? null); @endphp
        <div class="seg">
            <div class="top">
                <span class="fn">{{ $s['airline'] ?? $ticket->airline }} · {{ strtoupper($s['flight_number'] ?? '') }}</span>
                @if(!empty($s['cabin']))<span class="muted"> — {{ $s['cabin'] }}</span>@endif
                @php
                    $bag = [];
                    if (!empty($s['baggage'])) $bag[] = 'Check-in: '.$s['baggage'];
                    if (!empty($s['cabin_baggage'])) $bag[] = 'Cabin: '.$s['cabin_baggage'];
                @endphp
                @if($bag)<span class="muted" style="float:right">{{ implode(' · ', $bag) }}</span>@endif
            </div>
            <div class="body">
                <table class="row"><tr>
                    <td style="width:40%">
                        <div class="airport">{{ strtoupper($s['from_code'] ?? '') }}</div>
                        <div class="city">{{ $s['from_city'] ?? '' }}</div>
                        <div class="when" style="margin-top:6px">{{ $dep['date'] }} @if($dep['time']) · {{ $dep['time'] }}@endif</div>
                    </td>
                    <td style="width:20%" class="arrow">✈</td>
                    <td style="width:40%; text-align:right">
                        <div class="airport">{{ strtoupper($s['to_code'] ?? '') }}</div>
                        <div class="city">{{ $s['to_city'] ?? '' }}</div>
                        <div class="when" style="margin-top:6px">{{ $arr['date'] }} @if($arr['time']) · {{ $arr['time'] }}@endif</div>
                    </td>
                </tr></table>
            </div>
        </div>
    @endforeach

    <div style="margin-top:22px" class="section-label">Passengers</div>
    @php $showSeat = collect($ticket->passengers ?? [])->contains(fn ($p) => ! empty($p['seat'])); @endphp
    <table class="pax">
        <thead><tr><th style="width:45%">Name</th><th>Type</th><th>Ticket number</th>@if($showSeat)<th>Seat</th>@endif</tr></thead>
        <tbody>
            @foreach($ticket->passengers ?? [] as $p)
                <tr>
                    <td>{{ $p['name'] ?? '' }}</td>
                    <td>{{ ucfirst($p['type'] ?? 'adult') }}</td>
                    <td>{{ $p['ticket_number'] ?? '—' }}</td>
                    @if($showSeat)<td>{{ $p['seat'] ?? '—' }}</td>@endif
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($ticket->fare_total !== null)
        <table class="row" style="margin-top:16px"><tr><td style="text-align:right">
            <span class="muted">Total fare: </span>
            <span style="font-weight:bold; color:{{ $primary }}; font-size:14px">{{ $ticket->currency }} {{ number_format((float)$ticket->fare_total, 2) }}</span>
        </td></tr></table>
    @endif

    @if($ticket->notes)
        <div style="margin-top:22px"><div class="section-label">Notes</div><div>{{ $ticket->notes }}</div></div>
    @endif

    <div class="footer">
        This e-ticket is issued by {{ $company }}. Please carry a valid passport and arrive at the airport at least 3 hours before international departures.
        Check-in and baggage rules are set by the operating airline. For assistance contact {{ $email }} · {{ $phone }}.
    </div>
</div>
</body>
</html>
