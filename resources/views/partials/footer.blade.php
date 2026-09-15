<footer class="mt-24 bg-brand-ink text-slate-300">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-14">
        <div class="grid gap-10 md:grid-cols-4">
            <div class="md:col-span-1">
                <img src="/brand/logo-white.svg" alt="Window Trip" class="h-9 w-auto">
                <p class="mt-4 text-sm text-slate-400">{{ \App\Models\Setting::get('tagline') }} — visas, tickets and curated tour packages.</p>
            </div>
            <div>
                <h4 class="font-heading font-semibold text-white">Services</h4>
                <ul class="mt-4 space-y-2 text-sm">
                    <li><a href="{{ url('/visa') }}" class="hover:text-white transition">Visa Processing</a></li>
                    <li><a href="{{ url('/air-tickets') }}" class="hover:text-white transition">Air Tickets</a></li>
                    <li><a href="{{ url('/packages') }}" class="hover:text-white transition">Tour Packages</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-heading font-semibold text-white">Visa Destinations</h4>
                <ul class="mt-4 space-y-2 text-sm">
                    <li>India · USA · Europe</li>
                    <li>Thailand · Singapore</li>
                    <li>Malaysia · China</li>
                </ul>
            </div>
            <div>
                <h4 class="font-heading font-semibold text-white">Company</h4>
                <ul class="mt-4 space-y-2 text-sm">
                    <li><a href="{{ url('/about') }}" class="hover:text-white transition">About Us</a></li>
                    <li><a href="{{ url('/contact') }}" class="hover:text-white transition">Contact</a></li>
                    <li><a href="{{ route('terms') }}" class="hover:text-white transition">Terms &amp; Conditions</a></li>
                    <li><a href="{{ route('privacy') }}" class="hover:text-white transition">Privacy Policy</a></li>
                </ul>
            </div>
        </div>

        {{-- Disclaimer --}}
        <div class="mt-10 rounded-2xl bg-white/5 p-5 text-xs leading-relaxed text-slate-400">
            <strong class="text-slate-300">Disclaimer:</strong>
            {{ \App\Models\Setting::get('company_name') }} provides visa application <strong>processing and documentation assistance only</strong>.
            We do not issue visas and cannot guarantee approval — every visa decision rests solely with the relevant embassy or consulate.
            Service fees cover our processing work and are separate from government/embassy fees.
        </div>

        <div class="mt-8 flex flex-wrap items-center justify-between gap-3 border-t border-white/10 pt-6 text-sm text-slate-500">
            <span>&copy; {{ date('Y') }} {{ \App\Models\Setting::get('company_name') }}. All rights reserved.</span>
            <span class="flex gap-4">
                <a href="{{ route('terms') }}" class="hover:text-white">Terms</a>
                <a href="{{ route('privacy') }}" class="hover:text-white">Privacy</a>
            </span>
        </div>
    </div>
</footer>
