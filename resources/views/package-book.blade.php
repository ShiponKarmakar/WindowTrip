@extends('layouts.public')

@section('title', 'Book ' . $package->title . ' — Window Trip')
@section('meta_description', 'Book the ' . $package->title . ' tour package (' . $package->destination . ', ' . $package->nights . ') with Window Trip.')

@section('content')
    <section class="relative overflow-hidden brand-mesh">
        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12 lg:py-14">
            <nav class="text-sm text-slate-500">
                <a href="{{ url('/') }}" class="hover:text-brand-purple">Home</a> <span class="mx-2">/</span>
                <a href="{{ route('packages.index') }}" class="hover:text-brand-purple">Tour Packages</a> <span class="mx-2">/</span>
                <span class="text-brand-ink">{{ $package->title }}</span>
            </nav>
            <h1 class="mt-4 font-heading text-3xl sm:text-4xl font-extrabold text-brand-ink">Book <span class="text-gradient">{{ $package->title }}</span></h1>
        </div>
        <div class="gradient-rule"></div>
    </section>

    <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid gap-8 lg:grid-cols-3">
            {{-- Package summary --}}
            <div class="lg:col-span-1">
                <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm lg:sticky lg:top-28">
                    <div class="relative h-36" style="background-color: {{ $package->color ?? '#139dd5' }}">
                        <div class="absolute inset-0 dot-grid opacity-30"></div>
                        @if ($package->tag)
                            <span class="absolute left-4 top-4 rounded-full bg-white/90 px-3 py-1 text-xs font-semibold text-brand-ink">{{ $package->tag }}</span>
                        @endif
                        <span class="absolute right-4 bottom-3 text-4xl text-white/40">✈︎</span>
                    </div>
                    <div class="p-6">
                        <h2 class="font-heading text-lg font-semibold text-brand-ink">{{ $package->title }}</h2>
                        <p class="text-sm text-slate-500">{{ $package->destination }} · {{ $package->nights }}</p>
                        <div class="mt-3">
                            <span class="text-xs text-slate-500">From</span>
                            <div class="font-heading text-2xl font-bold text-brand-ink">৳{{ $package->price }}</div>
                            <span class="text-xs text-slate-400">per person (indicative)</span>
                        </div>
                        @if (!empty($package->includes))
                            <ul class="mt-4 space-y-1.5 border-t border-slate-100 pt-4 text-sm text-slate-600">
                                @foreach ($package->includes as $inc)
                                    <li class="flex items-center gap-2"><span class="text-emerald-500">✓</span> {{ $inc }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Booking form --}}
            <div class="lg:col-span-2">
                <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm sm:p-8">
                    <h2 class="font-heading text-xl font-bold text-brand-ink">Request your booking</h2>
                    <p class="mt-1 text-sm text-slate-500">Tell us your details — we’ll confirm availability and final pricing with you.</p>

                    @if (session('success'))
                        <div class="mt-6 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">{{ session('success') }}</div>
                    @endif

                    <form method="POST" action="{{ route('packages.book.store', $package->slug) }}" class="mt-6 space-y-5">
                        @csrf
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-slate-700">Your name</label>
                                <input name="name" value="{{ old('name') }}" required class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple" />
                                @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-slate-700">Email</label>
                                <input name="email" type="email" value="{{ old('email') }}" required class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple" />
                                @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div class="grid gap-5 sm:grid-cols-3">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-slate-700">Phone</label>
                                <input name="phone" value="{{ old('phone') }}" placeholder="+880…" class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple" />
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-slate-700">Travellers</label>
                                <input name="travellers" type="number" min="1" max="30" value="{{ old('travellers', 2) }}" required class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple" />
                                @error('travellers')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-slate-700">Preferred date</label>
                                <input name="travel_date" type="date" value="{{ old('travel_date') }}" class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple" />
                                @error('travel_date')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Special requests <span class="text-slate-400">(optional)</span></label>
                            <textarea name="message" rows="3" placeholder="Room preference, add-ons, anything else…" class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple">{{ old('message') }}</textarea>
                        </div>

                        <button type="submit" class="rounded-full bg-brand-gradient px-7 py-3.5 font-semibold text-white shadow-brand hover:opacity-90 transition">Request Booking</button>
                        <p class="text-xs text-slate-400">No payment now — this sends a booking request. Our team confirms availability and final price before anything is charged.</p>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
