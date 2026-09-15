@extends('layouts.public')

@section('title', 'Window Trip — Tourist Visas, Air Tickets & Tour Packages')
@section('meta_description', 'Window Trip is your complete travel partner — fast tourist visa processing, air tickets and curated tour packages for India, USA, Europe, Thailand, Singapore, Malaysia and China.')

@php
    $visas = \App\Models\VisaCountry::publicConfig();
    $destinations = [
        ['name' => 'India',      'flag' => '🇮🇳', 'time' => '5–7 days',  'from' => '3,500'],
        ['name' => 'USA',        'flag' => '🇺🇸', 'time' => '3–5 weeks', 'from' => '12,000'],
        ['name' => 'Europe',     'flag' => '🇪🇺', 'time' => '10–15 days','from' => '9,500'],
        ['name' => 'Thailand',   'flag' => '🇹🇭', 'time' => '3–5 days',  'from' => '4,000'],
        ['name' => 'Singapore',  'flag' => '🇸🇬', 'time' => '3–4 days',  'from' => '4,500'],
        ['name' => 'Malaysia',   'flag' => '🇲🇾', 'time' => '3–5 days',  'from' => '3,800'],
        ['name' => 'China',      'flag' => '🇨🇳', 'time' => '5–7 days',  'from' => '7,000'],
    ];
    $packages = \App\Models\Package::activeOrdered()->take(3)->get();
    $steps = [
        ['n' => '01', 'title' => 'Tell us your plan', 'desc' => 'Pick a destination and service — visa, ticket or package.'],
        ['n' => '02', 'title' => 'Share documents',   'desc' => 'Upload passport and papers securely from your portal.'],
        ['n' => '03', 'title' => 'We process it',     'desc' => 'Our team handles the application and keeps you posted.'],
        ['n' => '04', 'title' => 'You travel',        'desc' => 'Receive your visa, ticket or itinerary — and pack your bags.'],
    ];
    $features = [
        ['title' => 'Expert visa guidance',   'desc' => 'Country-specific checklists so nothing is missed.'],
        ['title' => 'Live status tracking',   'desc' => 'Follow every application in real time from your portal.'],
        ['title' => 'Secure document vault',  'desc' => 'Your passport scans are encrypted and private.'],
        ['title' => 'Dedicated support',      'desc' => 'A real person on every application and booking.'],
        ['title' => 'Transparent pricing',    'desc' => 'Clear fees up front — no hidden surprises.'],
        ['title' => '7 prime destinations',   'desc' => 'India, USA, Europe, Thailand, Singapore, Malaysia, China.'],
    ];
    $testimonials = [
        ['name' => 'Rafiul Hasan',   'role' => 'Thailand tourist visa', 'quote' => 'Got my Thailand visa in 4 days without visiting a single office. The portal kept me updated the whole time.'],
        ['name' => 'Nusrat Jahan',   'role' => 'Europe Schengen visa',  'quote' => 'They organised my entire Schengen file and the appointment. Smooth, professional and stress-free.'],
        ['name' => 'Tanvir Ahmed',   'role' => 'Singapore package',     'quote' => 'Booked the Singapore package for my family. Hotels, transfers, tickets — everything handled perfectly.'],
    ];
@endphp

@section('content')
    {{-- ================= HERO ================= --}}
    <section data-hero class="relative overflow-hidden" style="background:#121026">
        <div class="brand-mesh--dark absolute inset-0"></div>
        <div class="absolute inset-0 dot-grid opacity-20"></div>
        <div class="pointer-events-none absolute -top-40 -left-24 h-[28rem] w-[28rem] rounded-full bg-brand-blue/30 blur-3xl animate-blob"></div>
        <div class="pointer-events-none absolute -bottom-32 right-0 h-[30rem] w-[30rem] rounded-full bg-brand-magenta/30 blur-3xl animate-blob" style="animation-delay:-6s"></div>

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-16 pb-24 lg:pt-24 lg:pb-32">
            <div class="grid items-center gap-16 lg:grid-cols-12">
                {{-- Left --}}
                <div class="lg:col-span-6">
                    <span data-hero-badge class="inline-flex items-center gap-2 rounded-full glass-dark px-4 py-1.5 text-sm font-medium text-white">
                        <span class="relative flex h-2 w-2">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-400"></span>
                        </span>
                        Trusted by 12,000+ travellers
                    </span>

                    <h1 data-hero-title class="mt-6 font-heading text-5xl font-extrabold leading-[1.02] tracking-tight text-white sm:text-6xl lg:text-7xl">
                        <span class="block">Travel the world,</span>
                        <span class="block">visa worries
                            <span class="text-brand-300">gone.</span>
                        </span>
                    </h1>

                    <p data-hero-copy class="mt-6 max-w-xl text-lg text-slate-300">
                        Tourist visas, air tickets and curated tour packages for 7 top destinations —
                        fast, transparent and fully online.
                    </p>

                    {{-- Glass quick-search --}}
                    <form data-visa-jump data-hero-cta class="mt-8 max-w-xl rounded-2xl bg-white p-2.5 shadow-2xl sm:flex sm:items-center sm:gap-2">
                        <div class="flex min-w-0 flex-1 items-center gap-2 px-3">
                            <svg class="h-5 w-5 flex-none text-brand-purple" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="10" r="3"/><path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"/></svg>
                            <select class="w-full min-w-0 truncate border-0 bg-transparent py-2.5 text-brand-ink focus:ring-0">
                                <option value="">Where do you want to go?</option>
                                @foreach ($visas as $slug => $v)
                                    <option value="{{ $slug }}">{{ $v['flag'] }} {{ $v['name'] }} — {{ $v['subtitle'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="mt-2 w-full flex-none rounded-xl bg-brand-gradient px-6 py-3 font-semibold text-white shadow-brand transition hover:opacity-90 sm:mt-0 sm:w-auto">
                            Check Visa →
                        </button>
                    </form>

                    <div class="mt-6 flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-slate-400">
                        <a href="{{ url('/track') }}" class="inline-flex items-center gap-1.5 font-medium text-white hover:text-brand-blue">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="M21 21l-4.3-4.3"/></svg>
                            Track an application
                        </a>
                        <span class="inline-flex items-center gap-1.5 text-emerald-400">✓ <span class="text-slate-400">No embassy queues</span></span>
                        <span class="inline-flex items-center gap-1.5 text-emerald-400">✓ <span class="text-slate-400">98% success rate</span></span>
                    </div>
                </div>

                {{-- Right: live application mockup --}}
                <div class="lg:col-span-6">
                    <div data-hero-stat class="relative mx-auto max-w-md">
                        {{-- floating accent cards --}}
                        <div class="absolute -left-4 -top-5 z-20 hidden rounded-2xl bg-white px-4 py-3 shadow-xl sm:block">
                            <div class="flex items-center gap-2">
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">✓</span>
                                <div>
                                    <div class="text-xs text-slate-400">Visa approved</div>
                                    <div class="text-sm font-semibold text-brand-ink">in 4 days 🎉</div>
                                </div>
                            </div>
                        </div>
                        <div class="absolute -right-3 bottom-8 z-20 hidden rounded-2xl bg-white px-4 py-3 shadow-xl sm:block">
                            <div class="text-xs text-slate-400">Traveller rating</div>
                            <div class="flex items-center gap-1"><span class="font-heading text-lg font-bold text-brand-ink">4.9</span><span class="text-brand-magenta text-sm">★★★★★</span></div>
                        </div>
                        <svg data-hero-plane class="absolute -right-6 -top-8 z-0 h-24 w-24 text-white/15" viewBox="0 0 24 24" fill="currentColor"><path d="M21 16v-2l-8-5V3.5a1.5 1.5 0 0 0-3 0V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L11 19v-5.5L21 16z"/></svg>

                        {{-- main card --}}
                        <div class="relative z-10 overflow-hidden rounded-[2rem] bg-white shadow-2xl ring-1 ring-white/20">
                            <div class="relative bg-brand-gradient p-6">
                                <div class="absolute inset-0 dot-grid opacity-20"></div>
                                <div class="relative flex items-center justify-between text-white">
                                    <div>
                                        <div class="text-xs text-white/80">My application</div>
                                        <div class="font-mono text-sm font-semibold">WT-8K2P9XQM</div>
                                    </div>
                                    <span class="rounded-full bg-white/20 px-3 py-1 text-xs font-medium">🇹🇭 Thailand</span>
                                </div>
                                <div class="relative mt-4 flex items-end justify-between text-white">
                                    <div>
                                        <div class="text-3xl font-extrabold font-heading"><span data-count="12" data-count-suffix="k+">0</span></div>
                                        <div class="text-xs text-white/80">visas processed</div>
                                    </div>
                                    <span class="rounded-full bg-emerald-400/90 px-3 py-1 text-xs font-semibold text-emerald-950">● Approved</span>
                                </div>
                            </div>

                            {{-- status steps --}}
                            <div class="p-6">
                                @php
                                    $mock = [
                                        ['t' => 'Submitted', 'd' => 'Application received', 'done' => true],
                                        ['t' => 'Under review', 'd' => 'Documents verified', 'done' => true],
                                        ['t' => 'Lodged', 'd' => 'Sent to embassy', 'done' => true],
                                        ['t' => 'Approved', 'd' => 'Visa issued — ready to fly', 'done' => true],
                                    ];
                                @endphp
                                @foreach ($mock as $m)
                                    <div class="flex gap-3">
                                        <div class="flex flex-col items-center">
                                            <span class="flex h-7 w-7 items-center justify-center rounded-full bg-brand-gradient text-xs font-bold text-white">✓</span>
                                            @if (! $loop->last)<div class="my-1 h-6 w-0.5 bg-brand-purple/40"></div>@endif
                                        </div>
                                        <div class="pb-1">
                                            <div class="text-sm font-semibold text-brand-ink">{{ $m['t'] }}</div>
                                            <div class="text-xs text-slate-400">{{ $m['d'] }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Destinations marquee --}}
        <div class="relative border-t border-white/10 py-4">
            <div class="flex w-max animate-marquee gap-3">
                @foreach (array_merge($destinations, $destinations) as $d)
                    <a href="{{ url('/visa') }}" class="flex flex-none items-center gap-2 rounded-full glass-dark px-4 py-2 text-sm font-medium text-slate-200 hover:text-white">
                        <span class="text-lg">{{ $d['flag'] }}</span> {{ $d['name'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ================= SERVICES ================= --}}
    <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-24">
        <div class="text-center max-w-2xl mx-auto" data-animate>
            <span class="text-sm font-semibold uppercase tracking-wider text-brand-purple">What we do</span>
            <h2 class="mt-2 font-heading text-4xl font-bold text-brand-ink">Everything for your trip,<br>in one place</h2>
        </div>

        <div class="mt-14 grid gap-6 md:grid-cols-3" data-animate-group>
            @php
                $services = [
                    ['title' => 'Tourist Visa Processing', 'desc' => 'Document checklists, application handling and live status tracking for 7 destinations.', 'href' => '/visa', 'icon' => 'M9 12l2 2 4-4m5.6 1.4A9 9 0 1 1 12 3a9 9 0 0 1 8.6 11.4z'],
                    ['title' => 'Air Tickets', 'desc' => 'Tell us your route and dates — we source the best fare and issue your ticket.', 'href' => '/air-tickets', 'icon' => 'M21 16v-2l-8-5V3.5a1.5 1.5 0 0 0-3 0V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L11 19v-5.5L21 16z'],
                    ['title' => 'Tour Packages', 'desc' => 'Curated itineraries with stays, transfers and experiences included.', 'href' => '/packages', 'icon' => 'M3 7l9-4 9 4-9 4-9-4zm0 5l9 4 9-4M3 17l9 4 9-4'],
                ];
            @endphp
            @foreach ($services as $s)
                <a href="{{ url($s['href']) }}" data-animate class="group relative overflow-hidden rounded-3xl border border-slate-100 bg-white p-8 shadow-sm transition duration-300 hover:-translate-y-2 hover:shadow-brand">
                    <div class="absolute -right-8 -top-8 h-28 w-28 rounded-full bg-brand-gradient-soft transition group-hover:scale-150"></div>
                    <div class="relative flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-gradient text-white shadow-brand">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $s['icon'] }}"/></svg>
                    </div>
                    <h3 class="relative mt-6 font-heading text-xl font-semibold text-brand-ink group-hover:text-brand-purple transition">{{ $s['title'] }}</h3>
                    <p class="relative mt-3 text-slate-600">{{ $s['desc'] }}</p>
                    <span class="relative mt-5 inline-flex items-center gap-1 text-sm font-semibold text-brand-purple">Learn more <span class="transition group-hover:translate-x-1">→</span></span>
                </a>
            @endforeach
        </div>
    </section>

    {{-- ================= VISA DESTINATIONS ================= --}}
    <section class="bg-slate-50 py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-end justify-between gap-4" data-animate>
                <div>
                    <span class="text-sm font-semibold uppercase tracking-wider text-brand-purple">Visa destinations</span>
                    <h2 class="mt-2 font-heading text-4xl font-bold text-brand-ink">Pick your destination</h2>
                </div>
                <a href="{{ url('/visa') }}" class="text-sm font-semibold text-brand-purple hover:underline">View all countries →</a>
            </div>

            <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-4" data-animate-group>
                @foreach ($destinations as $d)
                    <a href="{{ url('/visa') }}" data-animate class="group rounded-3xl bg-white p-6 ring-1 ring-slate-100 transition hover:-translate-y-1.5 hover:shadow-brand hover:ring-brand-purple/30">
                        <div class="flex items-center justify-between">
                            <span class="text-4xl">{{ $d['flag'] }}</span>
                            <span class="rounded-full bg-brand-50 px-3 py-1 text-xs font-medium text-brand-600">{{ $d['time'] }}</span>
                        </div>
                        <h3 class="mt-4 font-heading text-lg font-semibold text-brand-ink group-hover:text-brand-purple transition">{{ $d['name'] }}</h3>
                        <p class="mt-1 text-sm text-slate-500">From <span class="font-semibold text-brand-ink">৳{{ $d['from'] }}</span></p>
                    </a>
                @endforeach
                <a href="{{ url('/visa') }}" data-animate class="flex flex-col items-start justify-center rounded-3xl bg-brand-gradient p-6 text-white shadow-brand transition hover:-translate-y-1.5">
                    <span class="font-heading text-lg font-semibold">Not sure where?</span>
                    <p class="mt-1 text-sm text-white/85">Talk to a visa expert today.</p>
                    <span class="mt-4 text-sm font-semibold">Get advice →</span>
                </a>
            </div>
        </div>
    </section>

    {{-- ================= HOW IT WORKS ================= --}}
    <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-24">
        <div class="text-center max-w-2xl mx-auto" data-animate>
            <span class="text-sm font-semibold uppercase tracking-wider text-brand-purple">How it works</span>
            <h2 class="mt-2 font-heading text-4xl font-bold text-brand-ink">From idea to boarding pass</h2>
        </div>
        <div class="mt-16 grid gap-8 md:grid-cols-4" data-animate-group>
            @foreach ($steps as $step)
                <div data-animate class="relative rounded-3xl border border-slate-100 bg-white p-7 shadow-sm">
                    <div class="font-heading text-5xl font-extrabold text-transparent bg-clip-text bg-brand-gradient">{{ $step['n'] }}</div>
                    <h3 class="mt-3 font-heading text-lg font-semibold text-brand-ink">{{ $step['title'] }}</h3>
                    <p class="mt-2 text-sm text-slate-600">{{ $step['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ================= FEATURED PACKAGES ================= --}}
    <section class="bg-slate-50 py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-end justify-between gap-4" data-animate>
                <div>
                    <span class="text-sm font-semibold uppercase tracking-wider text-brand-purple">Tour packages</span>
                    <h2 class="mt-2 font-heading text-4xl font-bold text-brand-ink">Featured trips</h2>
                </div>
                <a href="{{ url('/packages') }}" class="text-sm font-semibold text-brand-purple hover:underline">Browse all packages →</a>
            </div>
            <div class="mt-12 grid gap-8 md:grid-cols-3" data-animate-group>
                @foreach ($packages as $p)
                    <div data-animate class="group overflow-hidden rounded-3xl bg-white ring-1 ring-slate-100 shadow-sm transition hover:-translate-y-1.5 hover:shadow-brand">
                        <div class="relative h-52" style="background-color: {{ $p['color'] ?? '#139dd5' }}">
                            <div class="absolute inset-0 dot-grid opacity-30"></div>
                            <span class="absolute left-4 top-4 rounded-full bg-white/90 px-3 py-1 text-xs font-semibold text-brand-ink">{{ $p['tag'] }}</span>
                            <span class="absolute right-4 bottom-4 text-5xl text-white/40">✈︎</span>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <h3 class="font-heading text-lg font-semibold text-brand-ink">{{ $p['title'] }}</h3>
                                <span class="text-xs text-slate-500">{{ $p['nights'] }}</span>
                            </div>
                            <div class="mt-4 flex items-end justify-between">
                                <div>
                                    <div class="text-xs text-slate-500">From</div>
                                    <div class="font-heading text-xl font-bold text-brand-ink">৳{{ $p['price'] }}</div>
                                </div>
                                <a href="{{ route('packages.book', $p['slug']) }}" class="rounded-full bg-brand-gradient px-5 py-2.5 text-sm font-semibold text-white hover:opacity-90 transition">Book Now</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ================= WHY CHOOSE US ================= --}}
    <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-24">
        <div class="text-center max-w-2xl mx-auto" data-animate>
            <span class="text-sm font-semibold uppercase tracking-wider text-brand-purple">Why Window Trip</span>
            <h2 class="mt-2 font-heading text-4xl font-bold text-brand-ink">Built for confident travel</h2>
        </div>
        <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-3" data-animate-group>
            @foreach ($features as $f)
                <div data-animate class="flex gap-4 rounded-3xl border border-slate-100 bg-white p-6 transition hover:-translate-y-1 hover:border-brand-purple/30 hover:shadow-sm">
                    <div class="mt-0.5 flex h-11 w-11 flex-none items-center justify-center rounded-2xl bg-brand-gradient text-white">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <h3 class="font-heading font-semibold text-brand-ink">{{ $f['title'] }}</h3>
                        <p class="mt-1 text-sm text-slate-600">{{ $f['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ================= TESTIMONIALS ================= --}}
    <section class="bg-slate-50 py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto" data-animate>
                <span class="text-sm font-semibold uppercase tracking-wider text-brand-purple">Testimonials</span>
                <h2 class="mt-2 font-heading text-4xl font-bold text-brand-ink">Loved by travellers</h2>
            </div>
            <div class="mt-14 grid gap-8 md:grid-cols-3" data-animate-group>
                @foreach ($testimonials as $t)
                    <figure data-animate class="rounded-3xl bg-white p-7 ring-1 ring-slate-100 shadow-sm">
                        <div class="text-brand-magenta text-lg">★★★★★</div>
                        <blockquote class="mt-4 text-slate-700">“{{ $t['quote'] }}”</blockquote>
                        <figcaption class="mt-6 flex items-center gap-3">
                            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-brand-gradient font-semibold text-white">{{ \Illuminate\Support\Str::substr($t['name'], 0, 1) }}</div>
                            <div>
                                <div class="font-semibold text-brand-ink">{{ $t['name'] }}</div>
                                <div class="text-xs text-slate-500">{{ $t['role'] }}</div>
                            </div>
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ================= CTA BAND ================= --}}
    <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-20">
        <div data-animate class="relative overflow-hidden rounded-[2.5rem] bg-brand-gradient px-8 py-16 text-center shadow-brand">
            <div class="absolute inset-0 dot-grid opacity-20"></div>
            <div class="pointer-events-none absolute -top-10 -right-10 h-48 w-48 rounded-full bg-white/10 blur-2xl"></div>
            <div class="relative">
                <h2 class="font-heading text-3xl sm:text-5xl font-bold text-white">Ready to plan your next trip?</h2>
                <p class="mt-4 text-white/90 max-w-xl mx-auto">Start a visa application, request a ticket, or book a package — our team takes it from here.</p>
                <div class="mt-9 flex flex-wrap justify-center gap-4">
                    <a href="{{ route('register') }}" class="rounded-full bg-white px-8 py-4 font-semibold text-brand-ink hover:-translate-y-0.5 transition">Get Started Free</a>
                    <a href="{{ url('/contact') }}" class="rounded-full bg-white/15 px-8 py-4 font-semibold text-white ring-1 ring-white/40 hover:bg-white/25 transition">Talk to an Expert</a>
                </div>
            </div>
        </div>
    </section>
@endsection
