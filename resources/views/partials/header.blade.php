@php($navVisas = \App\Models\VisaCountry::publicConfig())
<header class="sticky top-0 z-50 bg-white/90 backdrop-blur border-b border-slate-100">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-20 items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center">
                <img src="/brand/logo-horizontal.svg" alt="Window Trip" class="h-10 w-auto">
            </a>

            <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-700">
                <a href="{{ url('/') }}" class="hover:text-brand-purple transition">Home</a>

                {{-- Visa dropdown / mega-menu --}}
                <div class="relative group">
                    <a href="{{ url('/visa') }}" class="flex items-center gap-1 hover:text-brand-purple transition group-hover:text-brand-purple">
                        Visa Processing
                        <svg class="h-4 w-4 transition group-hover:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </a>
                    {{-- hover bridge so the panel doesn't close in the gap --}}
                    <div class="invisible absolute left-1/2 top-full z-50 w-[34rem] -translate-x-1/2 pt-4 opacity-0 transition duration-150 group-hover:visible group-hover:opacity-100">
                        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-brand">
                            <div class="grid grid-cols-2 gap-1">
                                @foreach ($navVisas as $slug => $v)
                                    <a href="{{ route('visa.show', $slug) }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 hover:bg-brand-50 transition">
                                        <span class="text-2xl">{{ $v['flag'] }}</span>
                                        <span>
                                            <span class="block font-semibold text-brand-ink">{{ $v['name'] }}</span>
                                            <span class="block text-xs text-slate-500">{{ $v['processing'] }} · from ৳{{ $v['fee_from'] }}</span>
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                            <a href="{{ route('visa.index') }}" class="mt-3 flex items-center justify-between rounded-xl bg-brand-gradient px-4 py-3 text-sm font-semibold text-white">
                                View all visa destinations
                                <span>→</span>
                            </a>
                        </div>
                    </div>
                </div>

                <a href="{{ url('/air-tickets') }}" class="hover:text-brand-purple transition">Air Tickets</a>
                <a href="{{ url('/packages') }}" class="hover:text-brand-purple transition">Tour Packages</a>
                <a href="{{ url('/track') }}" class="hover:text-brand-purple transition">Track</a>
                <a href="{{ url('/contact') }}" class="hover:text-brand-purple transition">Contact</a>
            </nav>

            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ url('/dashboard') }}" class="hidden sm:inline rounded-full bg-brand-gradient px-5 py-2.5 text-sm font-semibold text-white shadow-brand hover:opacity-90 transition">My Portal</a>
                @else
                    <a href="{{ route('login') }}" class="hidden sm:inline text-sm font-medium text-slate-700 hover:text-brand-purple transition">Sign in</a>
                    <a href="{{ route('register') }}" class="hidden sm:inline rounded-full bg-brand-gradient px-5 py-2.5 text-sm font-semibold text-white shadow-brand hover:opacity-90 transition">Get Started</a>
                @endauth

                {{-- Hamburger (mobile only) --}}
                <button type="button" data-mobile-toggle aria-label="Open menu" aria-expanded="false" aria-controls="mobile-menu"
                    class="md:hidden inline-flex h-10 w-10 items-center justify-center rounded-xl text-brand-ink hover:bg-slate-100">
                    <svg data-menu-open class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg data-menu-close class="hidden h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div id="mobile-menu" data-mobile-menu class="hidden border-t border-slate-100 bg-white md:hidden">
        <nav class="mx-auto max-w-7xl space-y-1 px-4 py-4 text-sm font-medium text-slate-700">
            <a href="{{ url('/') }}" class="block rounded-xl px-3 py-2.5 hover:bg-brand-50">Home</a>
            <a href="{{ url('/visa') }}" class="block rounded-xl px-3 py-2.5 hover:bg-brand-50">Visa Processing</a>
            <div class="grid grid-cols-2 gap-1 px-2 pb-1">
                @foreach ($navVisas as $slug => $v)
                    <a href="{{ route('visa.show', $slug) }}" class="flex items-center gap-2 rounded-lg px-2 py-2 text-xs hover:bg-brand-50">
                        <span class="text-lg">{{ $v['flag'] }}</span> {{ $v['name'] }}
                    </a>
                @endforeach
            </div>
            <a href="{{ url('/air-tickets') }}" class="block rounded-xl px-3 py-2.5 hover:bg-brand-50">Air Tickets</a>
            <a href="{{ url('/packages') }}" class="block rounded-xl px-3 py-2.5 hover:bg-brand-50">Tour Packages</a>
            <a href="{{ url('/track') }}" class="block rounded-xl px-3 py-2.5 hover:bg-brand-50">Track</a>
            <a href="{{ url('/contact') }}" class="block rounded-xl px-3 py-2.5 hover:bg-brand-50">Contact</a>

            <div class="mt-3 grid gap-2 border-t border-slate-100 pt-4">
                @auth
                    <a href="{{ url('/dashboard') }}" class="rounded-full bg-brand-gradient px-5 py-3 text-center font-semibold text-white">My Portal</a>
                @else
                    <a href="{{ route('login') }}" class="rounded-full px-5 py-3 text-center font-semibold text-brand-ink ring-1 ring-slate-200">Sign in</a>
                    <a href="{{ route('register') }}" class="rounded-full bg-brand-gradient px-5 py-3 text-center font-semibold text-white">Get Started</a>
                @endauth
            </div>
        </nav>
    </div>
</header>
