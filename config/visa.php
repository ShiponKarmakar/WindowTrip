<?php

/*
|--------------------------------------------------------------------------
| Tourist visa destinations & requirements
|--------------------------------------------------------------------------
| Indicative tourist-visa data used to render the public visa pages.
| Figures are guidance only and must be confirmed per applicant.
| NOTE: In Phase 2 this moves to a DB table managed from the admin portal.
*/

return [

    'india' => [
        'name' => 'India',
        'flag' => '🇮🇳',
        'subtitle' => 'e-Tourist Visa',
        'processing' => '5–7 working days',
        'validity' => 'Up to 1 year (multiple entry)',
        'stay' => 'Up to 90 days per visit',
        'fee_from' => '3,500',
        'overview' => 'India offers a convenient online e-Tourist Visa for sightseeing, recreation and visiting friends or family. We prepare and submit your application and track it to grant.',
        'requirements' => [
            'Passport valid for at least 6 months with two blank pages',
            'Confirmed return air ticket',
            'Proof of residential address in Bangladesh',
            'Sufficient funds for the duration of stay',
            'Not travelling for employment or journalism',
        ],
        'documents' => [
            ['title' => 'Passport', 'desc' => 'Original passport, valid 6+ months, with 2 blank pages.'],
            ['title' => 'Photograph', 'desc' => 'Recent 2"×2" colour photo, white background, face centred.'],
            ['title' => 'Passport scan', 'desc' => 'Clear colour scan of the passport bio-data page.'],
            ['title' => 'Return ticket', 'desc' => 'Confirmed onward / return flight booking.'],
            ['title' => 'Address proof', 'desc' => 'Utility bill or NID showing current address.'],
        ],
        'photo_spec' => '2"×2" (51×51mm), white background, taken within last 3 months.',
        'faqs' => [
            ['q' => 'Do I need to visit the embassy?', 'a' => 'No. The India e-Tourist Visa is fully online — no embassy visit is required for most applicants.'],
            ['q' => 'How long can I stay?', 'a' => 'Tourist e-Visa typically allows stays of up to 90 days per visit.'],
        ],
    ],

    'usa' => [
        'name' => 'USA',
        'flag' => '🇺🇸',
        'subtitle' => 'B-2 Tourist Visa',
        'processing' => '3–5 weeks (after interview)',
        'validity' => 'Up to 5 years (multiple entry)',
        'stay' => 'Up to 180 days per entry',
        'fee_from' => '12,000',
        'overview' => 'The US B-2 visa is for tourism, leisure and visiting relatives. We complete your DS-160, schedule your interview and coach you through the documentation.',
        'requirements' => [
            'Passport valid 6+ months beyond intended stay',
            'Completed DS-160 confirmation',
            'Visa (MRV) fee payment receipt',
            'Strong ties to home country (job, family, assets)',
            'Proof of sufficient funds for the trip',
        ],
        'documents' => [
            ['title' => 'Passport', 'desc' => 'Current passport plus any old passports.'],
            ['title' => 'DS-160 confirmation', 'desc' => 'Printed confirmation page with barcode.'],
            ['title' => 'Photograph', 'desc' => '2"×2" (51×51mm) white background, last 6 months.'],
            ['title' => 'Financial proof', 'desc' => 'Bank statements (6 months), tax returns / pay slips.'],
            ['title' => 'Employment proof', 'desc' => 'NOC / employment letter or business documents.'],
            ['title' => 'Ties evidence', 'desc' => 'Property, family or other ties to home country.'],
        ],
        'photo_spec' => '2"×2" (51×51mm), white background, head 50–69% of frame.',
        'faqs' => [
            ['q' => 'Is an interview required?', 'a' => 'Yes — most applicants attend an in-person interview at the US Embassy. We prepare you thoroughly beforehand.'],
            ['q' => 'How early should I apply?', 'a' => 'Apply at least 2–3 months ahead, as interview slots can fill up.'],
        ],
    ],

    'europe' => [
        'name' => 'Europe',
        'flag' => '🇪🇺',
        'subtitle' => 'Schengen Tourist Visa',
        'processing' => '10–15 working days',
        'validity' => 'Trip duration (up to 90 days)',
        'stay' => '90 days within any 180-day period',
        'fee_from' => '9,500',
        'overview' => 'A single Schengen visa lets you travel across 29 European countries. We assemble your file, book the appointment and review every document before submission.',
        'requirements' => [
            'Passport valid 3+ months beyond departure, issued within 10 years',
            'Travel medical insurance with €30,000 minimum coverage',
            'Confirmed round-trip flight reservation',
            'Proof of accommodation for the whole stay',
            'Proof of sufficient funds and employment',
        ],
        'documents' => [
            ['title' => 'Passport', 'desc' => 'Valid 3+ months beyond return, with 2 blank pages.'],
            ['title' => 'Application form', 'desc' => 'Completed and signed Schengen application form.'],
            ['title' => 'Photographs', 'desc' => 'Two 35×45mm photos, white background.'],
            ['title' => 'Travel insurance', 'desc' => '€30,000 coverage valid across the Schengen area.'],
            ['title' => 'Flight & hotel', 'desc' => 'Round-trip reservation and confirmed accommodation.'],
            ['title' => 'Financial proof', 'desc' => 'Bank statements (6 months) and cover letter.'],
        ],
        'photo_spec' => '35×45mm, white background, neutral expression, recent.',
        'faqs' => [
            ['q' => 'Which country should I apply to?', 'a' => 'Apply to the country where you spend the most days, or the first country you enter if stays are equal.'],
            ['q' => 'Is travel insurance mandatory?', 'a' => 'Yes — Schengen rules require medical insurance with at least €30,000 coverage.'],
        ],
    ],

    'thailand' => [
        'name' => 'Thailand',
        'flag' => '🇹🇭',
        'subtitle' => 'Tourist Visa / e-Visa',
        'processing' => '3–5 working days',
        'validity' => '3 months from issue',
        'stay' => 'Up to 60 days (extendable)',
        'fee_from' => '4,000',
        'overview' => 'Thailand is a top short-haul destination. We handle your tourist visa or e-Visa application end to end so you can focus on the beaches and temples.',
        'requirements' => [
            'Passport valid 6+ months with one blank page',
            'Confirmed return air ticket',
            'Proof of accommodation for the stay',
            'Financial proof (≈ 20,000 THB per person)',
            'Recent passport-size photograph',
        ],
        'documents' => [
            ['title' => 'Passport', 'desc' => 'Valid 6+ months, at least one blank page.'],
            ['title' => 'Photograph', 'desc' => '4×6cm photo, white background, recent.'],
            ['title' => 'Return ticket', 'desc' => 'Confirmed onward / return flight.'],
            ['title' => 'Hotel booking', 'desc' => 'Accommodation confirmation for the trip.'],
            ['title' => 'Financial proof', 'desc' => 'Bank statement showing adequate funds.'],
        ],
        'photo_spec' => '4×6cm (or 2"×2"), white background, taken within 6 months.',
        'faqs' => [
            ['q' => 'Can my stay be extended?', 'a' => 'Yes — a 60-day tourist visa can usually be extended by 30 days at a local immigration office.'],
            ['q' => 'Is the e-Visa accepted from Bangladesh?', 'a' => 'Yes, Thailand’s e-Visa is available — we advise the best route for your case.'],
        ],
    ],

    'singapore' => [
        'name' => 'Singapore',
        'flag' => '🇸🇬',
        'subtitle' => 'Tourist Visa',
        'processing' => '3–4 working days',
        'validity' => 'Up to 2 years (multiple entry)',
        'stay' => 'Up to 30 days per visit',
        'fee_from' => '4,500',
        'overview' => 'Singapore visas are issued through authorised agents. As an experienced partner, we submit a clean, well-documented file for fast approval.',
        'requirements' => [
            'Passport valid 6+ months with two blank pages',
            'Completed Form 14A',
            'Confirmed flight and hotel bookings',
            'Bank statement showing sufficient funds',
            'Employment or business proof',
        ],
        'documents' => [
            ['title' => 'Passport', 'desc' => 'Valid 6+ months with 2 blank pages.'],
            ['title' => 'Form 14A', 'desc' => 'Completed and signed Singapore visa form.'],
            ['title' => 'Photograph', 'desc' => '35×45mm white-background photo.'],
            ['title' => 'Flight & hotel', 'desc' => 'Confirmed bookings for the visit.'],
            ['title' => 'Bank statement', 'desc' => 'Last 6 months, with adequate balance.'],
            ['title' => 'Cover letter', 'desc' => 'Letter stating purpose and trip details.'],
        ],
        'photo_spec' => '35×45mm, white background, matte or glossy finish.',
        'faqs' => [
            ['q' => 'Do I apply directly?', 'a' => 'Singapore tourist visas are processed via authorised agents — we submit on your behalf.'],
            ['q' => 'How long can I stay?', 'a' => 'The visa is multiple-entry but each stay is granted by immigration, typically up to 30 days.'],
        ],
    ],

    'malaysia' => [
        'name' => 'Malaysia',
        'flag' => '🇲🇾',
        'subtitle' => 'eVISA / eNTRI',
        'processing' => '3–5 working days',
        'validity' => '3 months from issue',
        'stay' => 'Up to 30 days',
        'fee_from' => '3,800',
        'overview' => 'Malaysia offers a simple online eVISA for tourism. We prepare and lodge your application and keep you updated until it is approved.',
        'requirements' => [
            'Passport valid 6+ months with one blank page',
            'Confirmed return air ticket',
            'Proof of accommodation',
            'Bank statement showing sufficient funds',
            'Recent passport-size photograph',
        ],
        'documents' => [
            ['title' => 'Passport', 'desc' => 'Valid 6+ months, one blank page.'],
            ['title' => 'Photograph', 'desc' => '35×50mm white-background photo.'],
            ['title' => 'Return ticket', 'desc' => 'Confirmed onward / return flight.'],
            ['title' => 'Hotel booking', 'desc' => 'Accommodation confirmation.'],
            ['title' => 'Bank statement', 'desc' => 'Recent statement showing funds.'],
        ],
        'photo_spec' => '35×50mm, white background, recent colour photo.',
        'faqs' => [
            ['q' => 'What is the difference between eVISA and eNTRI?', 'a' => 'eNTRI is a simpler single-entry facility for short visits; eVISA suits broader cases. We pick the right one for you.'],
            ['q' => 'Is it fully online?', 'a' => 'Yes — Malaysia’s tourist eVISA is processed entirely online.'],
        ],
    ],

    'china' => [
        'name' => 'China',
        'flag' => '🇨🇳',
        'subtitle' => 'L Tourist Visa',
        'processing' => '5–7 working days',
        'validity' => 'Up to 90 days from issue',
        'stay' => '30–60 days per entry',
        'fee_from' => '7,000',
        'overview' => 'The China L visa is for tourism and family visits. We prepare your application, itinerary and supporting documents for a smooth submission.',
        'requirements' => [
            'Passport valid 6+ months with two blank pages',
            'Confirmed round-trip flight booking',
            'Hotel bookings covering the itinerary',
            'Day-by-day travel itinerary',
            'Bank statement showing sufficient funds',
        ],
        'documents' => [
            ['title' => 'Passport', 'desc' => 'Valid 6+ months with 2 blank pages.'],
            ['title' => 'Photograph', 'desc' => '33×48mm white-background photo.'],
            ['title' => 'Flight booking', 'desc' => 'Confirmed round-trip reservation.'],
            ['title' => 'Hotel bookings', 'desc' => 'Confirmations covering the whole stay.'],
            ['title' => 'Itinerary', 'desc' => 'Detailed day-by-day travel plan.'],
            ['title' => 'Bank statement', 'desc' => 'Last 6 months with adequate balance.'],
        ],
        'photo_spec' => '33×48mm, white background, full face, recent.',
        'faqs' => [
            ['q' => 'Do I need an invitation letter?', 'a' => 'For tourism an itinerary and bookings are usually enough; an invitation helps for family visits.'],
            ['q' => 'Is biometrics required?', 'a' => 'Most applicants must give fingerprints at the visa centre — we book and brief you for it.'],
        ],
    ],

];
