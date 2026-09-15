@extends('layouts.public')

@section('title', 'Tour Packages — Curated Trips | Window Trip')
@section('meta_description', 'Explore curated tour packages from Window Trip — Thailand, Singapore, Malaysia, Dubai, Bali and more. Flights, hotels, transfers and experiences included.')

@section('content')
    <section class="relative overflow-hidden brand-mesh">
        <div class="pointer-events-none absolute -top-24 right-0 h-72 w-72 rounded-full bg-brand-blue/20 blur-3xl animate-blob"></div>
        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 lg:py-20">
            <nav class="text-sm text-slate-500"><a href="{{ url('/') }}" class="hover:text-brand-purple">Home</a> <span class="mx-2">/</span> <span class="text-brand-ink">Tour Packages</span></nav>
            <h1 class="mt-4 font-heading text-4xl sm:text-5xl font-extrabold text-brand-ink">Tour <span class="text-gradient">Packages</span></h1>
            <p class="mt-4 max-w-2xl text-lg text-slate-600">Handpicked trips with flights, stays, transfers and experiences — all arranged for you.</p>
        </div>
        <div class="gradient-rule"></div>
    </section>

    <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-14">
        <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3" data-animate-group>
            @foreach ($packages as $p)
                <div data-animate class="group flex flex-col overflow-hidden rounded-2xl bg-white ring-1 ring-slate-100 shadow-sm hover:shadow-brand transition">
                    <div class="relative h-48" style="background-color: {{ $p['color'] ?? '#139dd5' }}">
                        <div class="absolute inset-0 dot-grid opacity-30"></div>
                        <span class="absolute left-4 top-4 rounded-full bg-white/90 px-3 py-1 text-xs font-semibold text-brand-ink">{{ $p['tag'] }}</span>
                        <span class="absolute right-4 top-4 rounded-full bg-black/20 px-3 py-1 text-xs font-medium text-white">{{ $p['destination'] }}</span>
                        <span class="absolute right-4 bottom-4 text-5xl text-white/30">✈︎</span>
                    </div>
                    <div class="flex flex-1 flex-col p-6">
                        <div class="flex items-center justify-between">
                            <h3 class="font-heading text-lg font-semibold text-brand-ink">{{ $p['title'] }}</h3>
                            <span class="text-xs text-slate-500">{{ $p['nights'] }}</span>
                        </div>
                        <ul class="mt-4 space-y-1.5 text-sm text-slate-600">
                            @foreach ($p['includes'] as $inc)
                                <li class="flex items-center gap-2"><span class="text-emerald-500">✓</span> {{ $inc }}</li>
                            @endforeach
                        </ul>
                        <div class="mt-6 flex items-end justify-between border-t border-slate-100 pt-4">
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

        <div data-animate class="mt-14 rounded-3xl bg-brand-gradient px-8 py-12 text-center shadow-brand">
            <h2 class="font-heading text-2xl sm:text-3xl font-bold text-white">Want a custom itinerary?</h2>
            <p class="mt-3 text-white/90">Tell us your dream destination and budget — we’ll build a package just for you.</p>
            <a href="{{ route('contact') }}" class="mt-6 inline-block rounded-full bg-white px-7 py-3.5 font-semibold text-brand-ink hover:-translate-y-0.5 transition">Plan my trip</a>
        </div>
    </section>
@endsection
