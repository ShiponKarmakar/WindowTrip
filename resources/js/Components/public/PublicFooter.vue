<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const site = computed(() => page.props.site || {});
const year = new Date().getFullYear();
</script>

<template>
    <footer class="mt-24 bg-brand-ink text-slate-300">
        <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <div class="grid gap-10 md:grid-cols-4">
                <div>
                    <img src="/brand/logo-white.svg" alt="Window Trip" class="h-9 w-auto" />
                    <p class="mt-4 text-sm text-slate-400">{{ site.tagline }} — visas, tickets and curated tour packages.</p>
                </div>
                <div>
                    <h4 class="font-heading font-semibold text-white">Services</h4>
                    <ul class="mt-4 space-y-2 text-sm">
                        <li><Link :href="route('visa.index')" class="transition hover:text-white">Visa Processing</Link></li>
                        <li><Link :href="route('tickets.index')" class="transition hover:text-white">Air Tickets</Link></li>
                        <li><Link :href="route('packages.index')" class="transition hover:text-white">Tour Packages</Link></li>
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
                        <li><Link :href="route('contact')" class="transition hover:text-white">Contact</Link></li>
                        <li><Link :href="route('terms')" class="transition hover:text-white">Terms &amp; Conditions</Link></li>
                        <li><Link :href="route('privacy')" class="transition hover:text-white">Privacy Policy</Link></li>
                    </ul>
                </div>
            </div>

            <div class="mt-10 rounded-2xl bg-white/5 p-5 text-xs leading-relaxed text-slate-400">
                <strong class="text-slate-300">Disclaimer:</strong>
                {{ site.company }} provides visa application <strong>processing and documentation assistance only</strong>.
                We do not issue visas and cannot guarantee approval — every visa decision rests solely with the relevant embassy or consulate.
                Service fees cover our processing work and are separate from government/embassy fees.
            </div>

            <div class="mt-8 flex flex-wrap items-center justify-between gap-3 border-t border-white/10 pt-6 text-sm text-slate-500">
                <span>&copy; {{ year }} {{ site.company }}. All rights reserved.</span>
                <span class="flex gap-4">
                    <Link :href="route('terms')" class="hover:text-white">Terms</Link>
                    <Link :href="route('privacy')" class="hover:text-white">Privacy</Link>
                </span>
            </div>
        </div>
    </footer>
</template>
