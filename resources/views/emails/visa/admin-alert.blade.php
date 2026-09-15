<x-mail::message>
# New visa application 🛂

A new **{{ $application->countryName() }}** visa application has been submitted.

<x-mail::panel>
**Reference:** {{ $application->reference }}
**Applicant:** {{ $application->full_name }}
**Visa type:** {{ ucfirst($application->visa_type) }}
**Travellers:** {{ $application->travellers }}
**Nationality:** {{ $application->nationality }}
**Passport:** {{ $application->passport_number ?? '—' }}
**Email:** {{ $application->email }}
**Phone:** {{ $application->phone ?? '—' }}
@if($application->notes)
**Notes:** {{ $application->notes }}
@endif
</x-mail::panel>

<x-mail::button :url="config('app.url') . '/admin/applications'">
Review in Admin
</x-mail::button>

Window Trip
</x-mail::message>
