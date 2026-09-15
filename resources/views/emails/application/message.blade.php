<x-mail::message>
<div>{!! $body !!}</div>

<x-mail::panel>
Application reference: **{{ $application->reference }}** ({{ $application->countryName() }} {{ ucfirst($application->visa_type) }} Visa)
</x-mail::panel>

Warm regards,<br>
**{{ \App\Models\Setting::get('company_name') }}** — {{ \App\Models\Setting::get('tagline') }}
</x-mail::message>
