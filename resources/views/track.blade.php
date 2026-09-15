@extends('layouts.public')

@section('title', 'Track Your Application — Window Trip')
@section('meta_description', 'Track your Window Trip visa application status using your reference number and email.')

@php
    $steps = [
        ['key' => 'submitted', 'label' => 'Submitted', 'desc' => 'We received your application.'],
        ['key' => 'under_review', 'label' => 'Under Review', 'desc' => 'Our team is checking your file.'],
        ['key' => 'docs_required', 'label' => 'Processing', 'desc' => 'Documents verified & lodged.'],
        ['key' => 'approved', 'label' => 'Approved', 'desc' => 'Your visa is ready.'],
    ];
    $order = ['submitted' => 0, 'under_review' => 1, 'docs_required' => 2, 'approved' => 3];
    $currentIndex = isset($application) && $application ? ($order[$application['status']] ?? 0) : -1;
    $isRejected = isset($application) && $application && $application['status'] === 'rejected';
@endphp

@section('content')
    <section class="relative overflow-hidden brand-mesh">
        <div class="pointer-events-none absolute -top-24 right-0 h-72 w-72 rounded-full bg-brand-blue/20 blur-3xl animate-blob"></div>
        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 lg:py-20">
            <nav class="text-sm text-slate-500"><a href="{{ url('/') }}" class="hover:text-brand-purple">Home</a> <span class="mx-2">/</span> <span class="text-brand-ink">Track Application</span></nav>
            <h1 class="mt-4 font-heading text-4xl sm:text-5xl font-extrabold text-brand-ink">Track your <span class="text-gradient">application</span></h1>
            <p class="mt-4 max-w-2xl text-lg text-slate-600">Enter your reference number and email to see exactly where your application stands.</p>
        </div>
        <div class="gradient-rule"></div>
    </section>

    <section class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 py-14">
        {{-- Search form --}}
        <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm sm:p-8">
            <form method="POST" action="{{ route('track.check') }}" class="space-y-5">
                @csrf
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700">Reference / Application ID</label>
                        <input name="reference" value="{{ old('reference') }}" placeholder="WT-XXXXXXXX" required
                            class="w-full rounded-xl border-slate-200 uppercase focus:border-brand-purple focus:ring-brand-purple" />
                        @error('reference')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700">Email used on application</label>
                        <input name="email" type="email" value="{{ old('email') }}" placeholder="you@email.com" required
                            class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple" />
                        @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>

                @if (session('track_error'))
                    <div class="rounded-xl bg-red-50 px-4 py-3 text-sm font-medium text-red-600">{{ session('track_error') }}</div>
                @endif

                <button type="submit" class="inline-flex items-center gap-2 rounded-full bg-brand-gradient px-7 py-3.5 font-semibold text-white shadow-brand hover:opacity-90 transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="M21 21l-4.3-4.3"/></svg>
                    Track Application
                </button>
            </form>
        </div>

        {{-- Result --}}
        @if (isset($application) && $application)
            <div class="mt-8 rounded-2xl border border-slate-100 bg-white p-6 shadow-sm sm:p-8">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-3xl">{{ $application['flag'] }}</span>
                            <h2 class="font-heading text-xl font-bold text-brand-ink capitalize">{{ $application['country'] }} {{ $application['visa_type'] }} visa</h2>
                        </div>
                        <p class="mt-1 font-mono text-sm text-brand-purple">{{ $application['reference'] }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ $application['name'] }} · Submitted {{ $application['submitted'] }}</p>
                    </div>
                    <span class="rounded-full px-3 py-1 text-xs font-medium capitalize
                        @class([
                            'bg-sky-50 text-sky-600' => $application['status'] === 'submitted',
                            'bg-amber-50 text-amber-600' => $application['status'] === 'under_review',
                            'bg-orange-50 text-orange-600' => $application['status'] === 'docs_required',
                            'bg-emerald-50 text-emerald-600' => $application['status'] === 'approved',
                            'bg-red-50 text-red-500' => $application['status'] === 'rejected',
                        ])">
                        {{ str_replace('_', ' ', $application['status']) }}
                    </span>
                </div>

                {{-- Stepper --}}
                @if (! $isRejected)
                    <div class="mt-8 space-y-0">
                        @foreach ($steps as $i => $step)
                            <div class="flex gap-4">
                                <div class="flex flex-col items-center">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full text-sm font-semibold
                                        {{ $i <= $currentIndex ? 'bg-brand-gradient text-white' : 'bg-slate-100 text-slate-400' }}">
                                        @if ($i < $currentIndex) ✓ @else {{ $i + 1 }} @endif
                                    </div>
                                    @if (! $loop->last)
                                        <div class="my-1 h-10 w-0.5 {{ $i < $currentIndex ? 'bg-brand-purple' : 'bg-slate-100' }}"></div>
                                    @endif
                                </div>
                                <div class="pb-2">
                                    <div class="font-heading font-semibold {{ $i <= $currentIndex ? 'text-brand-ink' : 'text-slate-400' }}">{{ $step['label'] }}</div>
                                    <div class="text-sm text-slate-500">{{ $step['desc'] }}</div>
                                    @if ($i === $currentIndex)
                                        <div class="mt-1 inline-block rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-medium text-brand-600">Current · updated {{ $application['updated'] }}</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="mt-6 rounded-xl bg-red-50 px-5 py-4 text-sm text-red-600">
                        Unfortunately this application was not approved. Please <a href="{{ route('contact') }}" class="font-semibold underline">contact us</a> to discuss next steps.
                    </div>
                @endif

                <div class="mt-8 flex flex-wrap gap-3 border-t border-slate-100 pt-6">
                    <a href="{{ route('contact') }}" class="rounded-full bg-white px-5 py-2.5 text-sm font-semibold text-brand-ink ring-1 ring-slate-200 hover:ring-brand-purple/40 transition">Need help?</a>
                    <a href="{{ route('visa.index') }}" class="rounded-full bg-brand-gradient px-5 py-2.5 text-sm font-semibold text-white hover:opacity-90 transition">Start another application</a>
                </div>
            </div>
        @endif

        <p class="mt-6 text-center text-sm text-slate-400">Lost your reference? <a href="{{ route('contact') }}" class="font-medium text-brand-purple hover:underline">Contact us</a> and we’ll find it for you.</p>
    </section>
@endsection
