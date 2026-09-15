@extends('layouts.public')

@section('title', $country['name'] . ' Tourist Visa — Requirements & Documents | Window Trip')
@section('meta_description', $country['name'] . ' ' . $country['subtitle'] . ': requirements, document checklist, processing time (' . $country['processing'] . ') and fees. Apply with Window Trip.')

@section('content')
    {{-- Hero --}}
    <section class="relative overflow-hidden brand-mesh">
        <div class="pointer-events-none absolute -top-24 right-10 h-72 w-72 rounded-full bg-brand-blue/20 blur-3xl animate-blob"></div>
        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-14 lg:py-16">
            <nav class="text-sm text-slate-500">
                <a href="{{ url('/') }}" class="hover:text-brand-purple">Home</a>
                <span class="mx-2">/</span>
                <a href="{{ route('visa.index') }}" class="hover:text-brand-purple">Visa Processing</a>
                <span class="mx-2">/</span>
                <span class="text-brand-ink">{{ $country['name'] }}</span>
            </nav>

            <div class="mt-5 flex items-center gap-5">
                <span class="text-6xl">{{ $country['flag'] }}</span>
                <div>
                    <h1 class="font-heading text-4xl sm:text-5xl font-extrabold text-brand-ink">
                        {{ $country['name'] }} <span class="text-gradient">Visa</span>
                    </h1>
                    <p class="mt-1 text-lg text-slate-600">{{ $country['subtitle'] }}</p>
                </div>
            </div>
            <p class="mt-5 max-w-3xl text-slate-600">{{ $country['overview'] }}</p>
        </div>
        <div class="gradient-rule"></div>
    </section>

    {{-- Quick facts strip --}}
    <section class="border-b border-slate-100 bg-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6 grid grid-cols-2 md:grid-cols-4 gap-6">
            @php
                $facts = [
                    ['label' => 'Processing time', 'value' => $country['processing']],
                    ['label' => 'Visa validity', 'value' => $country['validity']],
                    ['label' => 'Permitted stay', 'value' => $country['stay']],
                    ['label' => 'Service fee from', 'value' => '৳' . $country['fee_from']],
                ];
            @endphp
            @foreach ($facts as $f)
                <div>
                    <div class="text-xs uppercase tracking-wide text-slate-400">{{ $f['label'] }}</div>
                    <div class="mt-1 font-heading font-semibold text-brand-ink">{{ $f['value'] }}</div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Main content --}}
    <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-14">
        <div class="grid lg:grid-cols-3 gap-10">
            {{-- Left: requirements + documents --}}
            <div class="lg:col-span-2 space-y-12">
                {{-- Eligibility / requirements --}}
                <div data-animate>
                    <h2 class="font-heading text-2xl font-bold text-brand-ink">Visa requirements</h2>
                    <p class="mt-2 text-slate-600">To qualify for the {{ $country['name'] }} {{ $country['subtitle'] }}, you should meet the following:</p>
                    <ul class="mt-6 space-y-3">
                        @foreach ($country['requirements'] as $req)
                            <li class="flex gap-3">
                                <span class="mt-0.5 flex h-6 w-6 flex-none items-center justify-center rounded-full bg-brand-50 text-brand-600">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </span>
                                <span class="text-slate-700">{{ $req }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Required documents checklist --}}
                <div data-animate>
                    <h2 class="font-heading text-2xl font-bold text-brand-ink">Required documents</h2>
                    <p class="mt-2 text-slate-600">Have these ready — you’ll upload them securely from your portal.</p>
                    <div class="mt-6 grid sm:grid-cols-2 gap-4" data-animate-group>
                        @foreach ($country['documents'] as $i => $doc)
                            <div data-animate class="flex gap-4 rounded-2xl border border-slate-100 p-5 hover:border-brand-purple/30 hover:shadow-sm transition">
                                <div class="flex h-9 w-9 flex-none items-center justify-center rounded-lg bg-brand-gradient text-sm font-bold text-white">
                                    {{ $i + 1 }}
                                </div>
                                <div>
                                    <h3 class="font-heading font-semibold text-brand-ink">{{ $doc['title'] }}</h3>
                                    <p class="mt-1 text-sm text-slate-600">{{ $doc['desc'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Photo spec callout --}}
                    <div class="mt-5 flex items-start gap-3 rounded-2xl bg-brand-gradient-soft p-5">
                        <svg class="h-6 w-6 flex-none text-brand-purple" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 0 1 2-2h2l1.5-2h7L19 7h0a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9z"/><circle cx="12" cy="13" r="3.2"/></svg>
                        <div>
                            <h3 class="font-heading font-semibold text-brand-ink">Photo specification</h3>
                            <p class="mt-1 text-sm text-slate-700">{{ $country['photo_spec'] }}</p>
                        </div>
                    </div>
                </div>

                {{-- FAQ --}}
                @if (! empty($country['faqs']))
                    <div data-animate>
                        <h2 class="font-heading text-2xl font-bold text-brand-ink">Frequently asked questions</h2>
                        <div class="mt-6 divide-y divide-slate-100 rounded-2xl border border-slate-100">
                            @foreach ($country['faqs'] as $faq)
                                <details class="group p-5">
                                    <summary class="flex cursor-pointer list-none items-center justify-between font-medium text-brand-ink">
                                        {{ $faq['q'] }}
                                        <svg class="h-5 w-5 text-brand-purple transition group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                    </summary>
                                    <p class="mt-3 text-slate-600">{{ $faq['a'] }}</p>
                                </details>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Right: sticky apply card --}}
            <aside class="lg:col-span-1">
                <div class="lg:sticky lg:top-28 space-y-6">
                    <div class="rounded-2xl bg-white p-6 ring-1 ring-slate-100 shadow-sm">
                        <div class="text-sm text-slate-500">Service fee from</div>
                        <div class="font-heading text-3xl font-bold text-brand-ink">৳{{ $country['fee_from'] }}</div>
                        <p class="mt-1 text-xs text-slate-500">Government / embassy fees billed separately.</p>

                        <a href="{{ route('visa.apply', $country['slug']) }}" class="mt-5 block rounded-full bg-brand-gradient px-6 py-3.5 text-center font-semibold text-white shadow-brand hover:opacity-90 transition">
                            Start Application
                        </a>
                        <a href="{{ url('/contact') }}" class="mt-3 block rounded-full bg-white px-6 py-3.5 text-center font-semibold text-brand-ink ring-1 ring-slate-200 hover:ring-brand-purple/40 transition">
                            Ask a Question
                        </a>

                        <ul class="mt-6 space-y-2 text-sm text-slate-600">
                            <li class="flex items-center gap-2"><span class="text-emerald-500">✓</span> Document review included</li>
                            <li class="flex items-center gap-2"><span class="text-emerald-500">✓</span> Live status tracking</li>
                            <li class="flex items-center gap-2"><span class="text-emerald-500">✓</span> Expert support</li>
                        </ul>
                    </div>

                    <div class="rounded-2xl p-6 text-white" style="background:#0f1f33">
                        <h4 class="font-heading text-sm font-semibold">⚠️ Please note</h4>
                        <p class="mt-2 text-sm text-white/80">
                            We provide visa <strong>processing &amp; documentation assistance only</strong>. We do not issue
                            visas and cannot guarantee approval — the final decision rests entirely with the embassy/consulate.
                            Information here is indicative and confirmed for your case before submission.
                        </p>
                    </div>
                </div>
            </aside>
        </div>
    </section>

    {{-- Other destinations --}}
    <section class="bg-slate-50 py-14">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <h2 class="font-heading text-2xl font-bold text-brand-ink">Other destinations</h2>
            <div class="mt-6 flex gap-4 overflow-x-auto pb-2">
                @foreach ($others as $o)
                    <a href="{{ route('visa.show', $o['slug']) }}" class="group flex-none w-44 rounded-2xl bg-white p-5 ring-1 ring-slate-100 hover:ring-brand-purple/30 hover:shadow-brand transition">
                        <span class="text-3xl">{{ $o['flag'] }}</span>
                        <div class="mt-3 font-heading font-semibold text-brand-ink group-hover:text-brand-purple transition">{{ $o['name'] }}</div>
                        <div class="text-xs text-slate-500">{{ $o['processing'] }}</div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endsection
