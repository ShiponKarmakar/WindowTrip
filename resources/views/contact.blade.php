@extends('layouts.public')

@section('title', 'Contact Window Trip — Visa, Tickets & Packages')
@section('meta_description', 'Get in touch with Window Trip for tourist visa processing, air tickets and tour packages. Send us a message and our travel experts will respond shortly.')

@section('content')
    <section class="relative overflow-hidden brand-mesh">
        <div class="pointer-events-none absolute -top-24 right-0 h-72 w-72 rounded-full bg-brand-blue/20 blur-3xl animate-blob"></div>
        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 lg:py-20">
            <nav class="text-sm text-slate-500"><a href="{{ url('/') }}" class="hover:text-brand-purple">Home</a> <span class="mx-2">/</span> <span class="text-brand-ink">Contact</span></nav>
            <h1 class="mt-4 font-heading text-4xl sm:text-5xl font-extrabold text-brand-ink">Let’s plan your <span class="text-gradient">next trip</span></h1>
            <p class="mt-4 max-w-2xl text-lg text-slate-600">Questions about a visa, ticket or package? Send us a message — our travel experts reply fast.</p>
        </div>
        <div class="gradient-rule"></div>
    </section>

    <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-14">
        <div class="grid gap-10 lg:grid-cols-3">
            <!-- Contact info -->
            <div class="space-y-6">
                @foreach ([
                    ['icon' => '📞', 'label' => 'Call us', 'value' => \App\Models\Setting::get('support_phone')],
                    ['icon' => '✉️', 'label' => 'Email', 'value' => \App\Models\Setting::get('support_email')],
                    ['icon' => '📍', 'label' => 'Office', 'value' => \App\Models\Setting::get('office_address')],
                    ['icon' => '🕐', 'label' => 'Hours', 'value' => \App\Models\Setting::get('office_hours')],
                ] as $info)
                    <div class="flex items-start gap-4 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                        <span class="text-2xl">{{ $info['icon'] }}</span>
                        <div>
                            <div class="text-xs uppercase tracking-wide text-slate-400">{{ $info['label'] }}</div>
                            <div class="font-medium text-brand-ink">{{ $info['value'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Form -->
            <div class="lg:col-span-2">
                <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm sm:p-8">
                    @if (session('success'))
                        <div class="mb-6 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">{{ session('success') }}</div>
                    @endif

                    <form method="POST" action="{{ route('contact.store') }}" class="space-y-5">
                        @csrf
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-slate-700">Name</label>
                                <input name="name" value="{{ old('name') }}" required class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple" />
                                @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-slate-700">Email</label>
                                <input name="email" type="email" value="{{ old('email') }}" required class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple" />
                                @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-slate-700">Phone <span class="text-slate-400">(optional)</span></label>
                                <input name="phone" value="{{ old('phone') }}" class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple" />
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-slate-700">Subject <span class="text-slate-400">(optional)</span></label>
                                <input name="subject" value="{{ old('subject') }}" class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple" />
                            </div>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Message</label>
                            <textarea name="message" rows="5" required class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple">{{ old('message') }}</textarea>
                            @error('message')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <button type="submit" class="rounded-full bg-brand-gradient px-7 py-3.5 font-semibold text-white shadow-brand hover:opacity-90 transition">Send message</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
