@extends('layouts.public')

@section('title', 'Air Tickets — Request Best Fares | Window Trip')
@section('meta_description', 'Request domestic and international air tickets with Window Trip. Tell us your route and dates and we source the best fares — no search hassle.')

@section('content')
    <section class="relative overflow-hidden brand-mesh">
        <div class="pointer-events-none absolute -top-24 right-0 h-72 w-72 rounded-full bg-brand-magenta/20 blur-3xl animate-blob"></div>
        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 lg:py-20">
            <nav class="text-sm text-slate-500"><a href="{{ url('/') }}" class="hover:text-brand-purple">Home</a> <span class="mx-2">/</span> <span class="text-brand-ink">Air Tickets</span></nav>
            <h1 class="mt-4 font-heading text-4xl sm:text-5xl font-extrabold text-brand-ink">Air <span class="text-gradient">Tickets</span></h1>
            <p class="mt-4 max-w-2xl text-lg text-slate-600">No endless searching. Just tell us where and when — we source the best fares and issue your ticket.</p>
        </div>
        <div class="gradient-rule"></div>
    </section>

    <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-14">
        <div class="grid gap-10 lg:grid-cols-3">
            <!-- Why us -->
            <div class="space-y-4">
                @foreach ([
                    ['🎫', 'Best available fares', 'We compare airlines and unpublished deals for you.'],
                    ['⚡', 'Fast turnaround', 'Get fare options the same day, often within hours.'],
                    ['🌍', 'Domestic & international', 'Any route, any airline — economy to business.'],
                    ['🤝', 'Human support', 'A real agent handles your booking end to end.'],
                ] as $b)
                    <div class="flex items-start gap-4 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                        <span class="text-2xl">{{ $b[0] }}</span>
                        <div>
                            <div class="font-heading font-semibold text-brand-ink">{{ $b[1] }}</div>
                            <p class="mt-0.5 text-sm text-slate-600">{{ $b[2] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Request form -->
            <div class="lg:col-span-2">
                <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm sm:p-8">
                    <h2 class="font-heading text-xl font-bold text-brand-ink">Request a ticket</h2>
                    <p class="mt-1 text-sm text-slate-500">Fill in your trip and we’ll send you fare options.</p>

                    @if (session('success'))
                        <div class="mt-6 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">{{ session('success') }}</div>
                    @endif

                    <form method="POST" action="{{ route('tickets.store') }}" class="mt-6 space-y-5">
                        @csrf
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 text-sm font-medium text-slate-700">
                                <input type="radio" name="trip_type" value="round_trip" {{ old('trip_type', 'round_trip') === 'round_trip' ? 'checked' : '' }} class="text-brand-purple focus:ring-brand-purple"> Round trip
                            </label>
                            <label class="flex items-center gap-2 text-sm font-medium text-slate-700">
                                <input type="radio" name="trip_type" value="one_way" {{ old('trip_type') === 'one_way' ? 'checked' : '' }} class="text-brand-purple focus:ring-brand-purple"> One way
                            </label>
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-slate-700">From</label>
                                <input name="from" value="{{ old('from') }}" placeholder="Dhaka (DAC)" required class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple" />
                                @error('from')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-slate-700">To</label>
                                <input name="to" value="{{ old('to') }}" placeholder="Bangkok (BKK)" required class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple" />
                                @error('to')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="grid gap-5 sm:grid-cols-3">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-slate-700">Departure</label>
                                <input name="depart_date" type="date" value="{{ old('depart_date') }}" required class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple" />
                                @error('depart_date')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-slate-700">Return <span class="text-slate-400">(optional)</span></label>
                                <input name="return_date" type="date" value="{{ old('return_date') }}" class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple" />
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-slate-700">Passengers</label>
                                <input name="passengers" type="number" min="1" max="20" value="{{ old('passengers', 1) }}" required class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple" />
                            </div>
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-slate-700">Cabin class</label>
                                <select name="cabin" class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple">
                                    @foreach (['Economy', 'Premium Economy', 'Business', 'First'] as $c)
                                        <option value="{{ $c }}" {{ old('cabin') === $c ? 'selected' : '' }}>{{ $c }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

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
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Phone <span class="text-slate-400">(optional)</span></label>
                            <input name="phone" value="{{ old('phone') }}" class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Notes <span class="text-slate-400">(optional)</span></label>
                            <textarea name="notes" rows="2" class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple">{{ old('notes') }}</textarea>
                        </div>

                        <button type="submit" class="rounded-full bg-brand-gradient px-7 py-3.5 font-semibold text-white shadow-brand hover:opacity-90 transition">Request fares</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
