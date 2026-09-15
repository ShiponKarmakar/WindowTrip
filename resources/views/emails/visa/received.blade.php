<x-mail::message>
# Thank you, {{ $application->full_name }} 👋

We’ve received your **{{ $application->countryName() }} {{ ucfirst($application->visa_type) }} Visa** application. Our team will review it and get in touch shortly.

**Reference number:** {{ $application->reference }}

<x-mail::panel>
**Destination:** {{ $application->countryName() }}
**Visa type:** {{ ucfirst($application->visa_type) }}
**Travellers:** {{ $application->travellers }}
@if($application->travel_date)
**Intended travel:** {{ $application->travel_date->format('d M Y') }}
@endif
</x-mail::panel>

Keep your reference number handy — you can use it to track your application status with us.

> **Please note:** We provide visa processing and documentation assistance only. We do not issue visas and cannot guarantee approval — the final decision rests with the embassy/consulate.

<x-mail::button :url="config('app.url') . '/visa/' . $application->country">
View {{ $application->countryName() }} Visa Info
</x-mail::button>

Safe travels,<br>
**Window Trip** — Your Complete Travel Partner
</x-mail::message>
