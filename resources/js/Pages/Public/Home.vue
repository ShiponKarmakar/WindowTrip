<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    visas: { type: Array, default: () => [] },
    packages: { type: Array, default: () => [] },
});

const jumpSlug = ref('');
function jump() {
    if (jumpSlug.value) router.visit(route('visa.show', jumpSlug.value));
}

const services = [
    { title: 'Tourist Visa Processing', desc: 'Document checklists, application handling and live status tracking for 7 destinations.', href: 'visa.index', icon: 'M9 12l2 2 4-4m5.6 1.4A9 9 0 1 1 12 3a9 9 0 0 1 8.6 11.4z' },
    { title: 'Air Tickets', desc: 'Tell us your route and dates — we source the best fare and issue your ticket.', href: 'tickets.index', icon: 'M21 16v-2l-8-5V3.5a1.5 1.5 0 0 0-3 0V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L11 19v-5.5L21 16z' },
    { title: 'Tour Packages', desc: 'Curated itineraries with stays, transfers and experiences included.', href: 'packages.index', icon: 'M3 7l9-4 9 4-9 4-9-4zm0 5l9 4 9-4M3 17l9 4 9-4' },
];
const steps = [
    { n: '01', title: 'Tell us your plan', desc: 'Pick a destination and service — visa, ticket or package.' },
    { n: '02', title: 'Share documents', desc: 'Upload passport and papers securely from your portal.' },
    { n: '03', title: 'We process it', desc: 'Our team handles the application and keeps you posted.' },
    { n: '04', title: 'You travel', desc: 'Receive your visa, ticket or itinerary — and pack your bags.' },
];
const features = [
    { title: 'Expert visa guidance', desc: 'Country-specific checklists so nothing is missed.' },
    { title: 'Live status tracking', desc: 'Follow every application in real time from your portal.' },
    { title: 'Secure document vault', desc: 'Your passport scans are encrypted and private.' },
    { title: 'Dedicated support', desc: 'A real person on every application and booking.' },
    { title: 'Transparent pricing', desc: 'Clear fees up front — no hidden surprises.' },
    { title: '7 prime destinations', desc: 'India, USA, Europe, Thailand, Singapore, Malaysia, China.' },
];
const testimonials = [
    { name: 'Rafiul Hasan', role: 'Thailand tourist visa', quote: 'Got my Thailand visa in 4 days without visiting a single office. The portal kept me updated the whole time.' },
    { name: 'Nusrat Jahan', role: 'Europe Schengen visa', quote: 'They organised my entire Schengen file and the appointment. Smooth, professional and stress-free.' },
    { name: 'Tanvir Ahmed', role: 'Singapore package', quote: 'Booked the Singapore package for my family. Hotels, transfers, tickets — everything handled perfectly.' },
];
const marquee = [...props.visas, ...props.visas];

const stats = [
    { count: 12, suffix: 'k+', label: 'Visas processed' },
    { count: props.visas.length || 7, suffix: '', label: 'Visa destinations' },
    { count: 4800, suffix: '+', label: 'Happy travellers' },
    { count: 98, suffix: '%', label: 'On-time delivery' },
];
</script>

<template>
    <Head title="Window Trip — Tourist Visas, Air Tickets & Tour Packages" />
    <PublicLayout>
        <!-- HERO -->
        <section class="relative overflow-hidden" style="background:#121026">
            <div class="brand-mesh--dark absolute inset-0"></div>
            <div class="dot-grid absolute inset-0 opacity-20"></div>
            <div class="pointer-events-none absolute -left-24 -top-40 h-[28rem] w-[28rem] animate-blob rounded-full bg-brand-blue/30 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-32 right-0 h-[30rem] w-[30rem] animate-blob rounded-full bg-brand-magenta/30 blur-3xl" style="animation-delay:-6s"></div>

            <div class="relative mx-auto max-w-7xl px-4 pb-24 pt-16 sm:px-6 lg:px-8 lg:pb-32 lg:pt-24">
                <div class="grid items-center gap-16 lg:grid-cols-12">
                    <div class="lg:col-span-6">
                        <span data-animate class="inline-flex items-center gap-2 rounded-full glass-dark px-4 py-1.5 text-sm font-medium text-white">
                            <span class="relative flex h-2 w-2"><span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span><span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-400"></span></span>
                            Trusted by 12,000+ travellers
                        </span>
                        <h1 data-animate class="mt-6 font-heading text-5xl font-extrabold leading-[1.02] tracking-tight text-white sm:text-6xl lg:text-7xl">
                            <span class="block">Travel the world,</span>
                            <span class="block">visa worries <span class="text-brand-300">gone.</span></span>
                        </h1>
                        <p data-animate class="mt-6 max-w-xl text-lg text-slate-300">Tourist visas, air tickets and curated tour packages for 7 top destinations — fast, transparent and fully online.</p>

                        <form data-animate @submit.prevent="jump" class="mt-8 max-w-xl rounded-2xl bg-white p-2.5 shadow-2xl sm:flex sm:items-center sm:gap-2">
                            <div class="flex min-w-0 flex-1 items-center gap-2 px-3">
                                <svg class="h-5 w-5 flex-none text-brand-purple" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="10" r="3" /><path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z" /></svg>
                                <select v-model="jumpSlug" aria-label="Choose a destination" class="w-full min-w-0 truncate border-0 bg-transparent py-2.5 text-brand-ink focus:ring-0">
                                    <option value="">Where do you want to go?</option>
                                    <option v-for="v in visas" :key="v.slug" :value="v.slug">{{ v.flag }} {{ v.name }}</option>
                                </select>
                            </div>
                            <button type="submit" class="mt-2 w-full flex-none rounded-xl bg-brand-gradient px-6 py-3 font-semibold text-white shadow-brand transition hover:opacity-90 sm:mt-0 sm:w-auto">Check Visa →</button>
                        </form>

                        <div data-animate class="mt-6 flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-slate-400">
                            <Link :href="route('track')" class="inline-flex items-center gap-1.5 font-medium text-white hover:text-brand-blue">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7" /><path stroke-linecap="round" d="M21 21l-4.3-4.3" /></svg>
                                Track an application
                            </Link>
                            <span class="inline-flex items-center gap-1.5 text-emerald-400">✓ <span class="text-slate-400">No embassy queues</span></span>
                            <span class="inline-flex items-center gap-1.5 text-emerald-400">✓ <span class="text-slate-400">98% success rate</span></span>
                        </div>
                    </div>

                    <div class="lg:col-span-6">
                        <div data-animate class="relative mx-auto max-w-md">
                            <div class="absolute -left-4 -top-5 z-20 hidden rounded-2xl bg-white px-4 py-3 shadow-xl sm:block">
                                <div class="flex items-center gap-2">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">✓</span>
                                    <div><div class="text-xs text-slate-400">Visa approved</div><div class="text-sm font-semibold text-brand-ink">in 4 days 🎉</div></div>
                                </div>
                            </div>
                            <div class="absolute bottom-8 -right-3 z-20 hidden rounded-2xl bg-white px-4 py-3 shadow-xl sm:block">
                                <div class="text-xs text-slate-400">Traveller rating</div>
                                <div class="flex items-center gap-1"><span class="font-heading text-lg font-bold text-brand-ink">4.9</span><span class="text-sm text-brand-magenta">★★★★★</span></div>
                            </div>
                            <div class="relative z-10 overflow-hidden rounded-[2rem] bg-white shadow-2xl ring-1 ring-white/20">
                                <div class="relative bg-brand-gradient p-6">
                                    <div class="dot-grid absolute inset-0 opacity-20"></div>
                                    <div class="relative flex items-center justify-between text-white">
                                        <div><div class="text-xs text-white/80">My application</div><div class="font-mono text-sm font-semibold">WT-8K2P9XQM</div></div>
                                        <span class="rounded-full bg-white/20 px-3 py-1 text-xs font-medium">🇹🇭 Thailand</span>
                                    </div>
                                    <div class="relative mt-4 flex items-end justify-between text-white">
                                        <div><div class="font-heading text-3xl font-extrabold"><span data-count="12" data-count-suffix="k+">0</span></div><div class="text-xs text-white/80">visas processed</div></div>
                                        <span class="rounded-full bg-emerald-400/90 px-3 py-1 text-xs font-semibold text-emerald-950">● Approved</span>
                                    </div>
                                </div>
                                <div class="p-6">
                                    <div v-for="(m, i) in [['Submitted','Application received'],['Under review','Documents verified'],['Lodged','Sent to embassy'],['Approved','Visa issued — ready to fly']]" :key="i" class="flex gap-3">
                                        <div class="flex flex-col items-center">
                                            <span class="flex h-7 w-7 items-center justify-center rounded-full bg-brand-gradient text-xs font-bold text-white">✓</span>
                                            <div v-if="i < 3" class="my-1 h-6 w-0.5 bg-brand-purple/40"></div>
                                        </div>
                                        <div class="pb-1"><div class="text-sm font-semibold text-brand-ink">{{ m[0] }}</div><div class="text-xs text-slate-400">{{ m[1] }}</div></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative border-t border-white/10 py-4">
                <div class="flex w-max animate-marquee gap-3">
                    <Link v-for="(d, i) in marquee" :key="i" :href="route('visa.show', d.slug)" class="flex flex-none items-center gap-2 rounded-full glass-dark px-4 py-2 text-sm font-medium text-slate-200 hover:text-white">
                        <span class="text-lg">{{ d.flag }}</span> {{ d.name }}
                    </Link>
                </div>
            </div>
        </section>

        <!-- SERVICES -->
        <section class="mx-auto max-w-7xl px-4 py-24 sm:px-6 lg:px-8">
            <div data-animate class="mx-auto max-w-2xl text-center">
                <span class="text-sm font-semibold uppercase tracking-wider text-brand-purple">What we do</span>
                <h2 class="mt-2 font-heading text-4xl font-bold text-brand-ink">Everything for your trip,<br>in one place</h2>
            </div>
            <div class="mt-14 grid gap-6 md:grid-cols-3" data-animate-group>
                <Link v-for="s in services" :key="s.title" :href="route(s.href)" data-animate class="group relative overflow-hidden rounded-3xl border border-slate-100 bg-white p-8 shadow-sm transition duration-300 hover:-translate-y-2 hover:shadow-brand">
                    <div class="absolute -right-8 -top-8 h-28 w-28 rounded-full bg-brand-gradient-soft transition group-hover:scale-150"></div>
                    <div class="relative flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-gradient text-white shadow-brand">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" :d="s.icon" /></svg>
                    </div>
                    <h3 class="relative mt-6 font-heading text-xl font-semibold text-brand-ink transition group-hover:text-brand-purple">{{ s.title }}</h3>
                    <p class="relative mt-3 text-slate-600">{{ s.desc }}</p>
                    <span class="relative mt-5 inline-flex items-center gap-1 text-sm font-semibold text-brand-purple">Learn more <span class="transition group-hover:translate-x-1">→</span></span>
                </Link>
            </div>
        </section>

        <!-- DESTINATIONS -->
        <section class="bg-slate-50 py-24">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div data-animate class="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <span class="text-sm font-semibold uppercase tracking-wider text-brand-purple">Visa destinations</span>
                        <h2 class="mt-2 font-heading text-4xl font-bold text-brand-ink">Pick your destination</h2>
                    </div>
                    <Link :href="route('visa.index')" class="text-sm font-semibold text-brand-purple hover:underline">View all countries →</Link>
                </div>
                <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-4" data-animate-group>
                    <Link v-for="d in visas" :key="d.slug" :href="route('visa.show', d.slug)" data-animate class="group rounded-3xl bg-white p-6 ring-1 ring-slate-100 transition hover:-translate-y-1.5 hover:shadow-brand hover:ring-brand-purple/30">
                        <div class="flex items-center justify-between">
                            <span class="text-4xl">{{ d.flag }}</span>
                            <span class="rounded-full bg-brand-50 px-3 py-1 text-xs font-medium text-brand-600">{{ d.processing }}</span>
                        </div>
                        <h3 class="mt-4 font-heading text-lg font-semibold text-brand-ink transition group-hover:text-brand-purple">{{ d.name }}</h3>
                        <p class="mt-1 text-sm text-slate-500">From <span class="font-semibold text-brand-ink">৳{{ d.fee_from }}</span></p>
                    </Link>
                    <Link :href="route('contact')" data-animate class="flex flex-col items-start justify-center rounded-3xl bg-brand-gradient p-6 text-white shadow-brand transition hover:-translate-y-1.5">
                        <span class="font-heading text-lg font-semibold">Not sure where?</span>
                        <p class="mt-1 text-sm text-white/85">Talk to a visa expert today.</p>
                        <span class="mt-4 text-sm font-semibold">Get advice →</span>
                    </Link>
                </div>
            </div>
        </section>

        <!-- HOW IT WORKS -->
        <section class="mx-auto max-w-7xl px-4 py-24 sm:px-6 lg:px-8">
            <div data-animate class="mx-auto max-w-2xl text-center">
                <span class="text-sm font-semibold uppercase tracking-wider text-brand-purple">How it works</span>
                <h2 class="mt-2 font-heading text-4xl font-bold text-brand-ink">From idea to boarding pass</h2>
            </div>
            <div class="relative mt-16">
                <!-- connector line (desktop) -->
                <div class="pointer-events-none absolute inset-x-8 top-14 hidden h-0.5 bg-gradient-to-r from-brand-purple/10 via-brand-purple/40 to-brand-purple/10 md:block"></div>
                <div class="grid gap-8 md:grid-cols-4" data-animate-group>
                    <div v-for="s in steps" :key="s.n" data-animate class="group relative rounded-3xl border border-slate-100 bg-white p-7 shadow-sm transition duration-300 hover:-translate-y-2 hover:border-brand-purple/30 hover:shadow-brand">
                        <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-gradient font-heading text-xl font-extrabold text-white shadow-brand transition duration-300 group-hover:scale-110 group-hover:rotate-3">{{ s.n }}</div>
                        <h3 class="font-heading text-lg font-semibold text-brand-ink">{{ s.title }}</h3>
                        <p class="mt-2 text-sm text-slate-600">{{ s.desc }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- LIVE STATS -->
        <section class="relative overflow-hidden py-20" style="background:#121026">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-10 text-center sm:grid-cols-2 lg:grid-cols-4" data-animate-group>
                    <div v-for="st in stats" :key="st.label" data-animate>
                        <div class="font-heading text-5xl font-extrabold text-white sm:text-6xl">
                            <span :data-count="st.count" :data-count-suffix="st.suffix">0</span>
                        </div>
                        <div class="mt-2 text-sm font-medium uppercase tracking-wider text-white/60">{{ st.label }}</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- PACKAGES -->
        <section class="bg-slate-50 py-24">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div data-animate class="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <span class="text-sm font-semibold uppercase tracking-wider text-brand-purple">Tour packages</span>
                        <h2 class="mt-2 font-heading text-4xl font-bold text-brand-ink">Featured trips</h2>
                    </div>
                    <Link :href="route('packages.index')" class="text-sm font-semibold text-brand-purple hover:underline">Browse all packages →</Link>
                </div>
                <div class="mt-12 grid gap-8 md:grid-cols-3" data-animate-group>
                    <div v-for="p in packages" :key="p.slug" data-animate class="group overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-100 transition hover:-translate-y-1.5 hover:shadow-brand">
                        <div class="relative h-52" :style="{ backgroundColor: p.color || '#139dd5' }">
                            <div class="dot-grid absolute inset-0 opacity-30"></div>
                            <span class="absolute left-4 top-4 rounded-full bg-white/90 px-3 py-1 text-xs font-semibold text-brand-ink">{{ p.tag }}</span>
                            <span class="absolute bottom-4 right-4 text-5xl text-white/40">✈︎</span>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <h3 class="font-heading text-lg font-semibold text-brand-ink">{{ p.title }}</h3>
                                <span class="text-xs text-slate-500">{{ p.nights }}</span>
                            </div>
                            <div class="mt-4 flex items-end justify-between">
                                <div><div class="text-xs text-slate-500">From</div><div class="font-heading text-xl font-bold text-brand-ink">৳{{ p.price }}</div></div>
                                <Link :href="route('packages.book', p.slug)" class="rounded-full bg-brand-gradient px-5 py-2.5 text-sm font-semibold text-white transition hover:opacity-90">Book Now</Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- WHY US -->
        <section class="mx-auto max-w-7xl px-4 py-24 sm:px-6 lg:px-8">
            <div data-animate class="mx-auto max-w-2xl text-center">
                <span class="text-sm font-semibold uppercase tracking-wider text-brand-purple">Why Window Trip</span>
                <h2 class="mt-2 font-heading text-4xl font-bold text-brand-ink">Built for confident travel</h2>
            </div>
            <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-3" data-animate-group>
                <div v-for="f in features" :key="f.title" data-animate class="flex gap-4 rounded-3xl border border-slate-100 bg-white p-6 transition hover:-translate-y-1 hover:border-brand-purple/30 hover:shadow-sm">
                    <div class="mt-0.5 flex h-11 w-11 flex-none items-center justify-center rounded-2xl bg-brand-gradient text-white">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                    </div>
                    <div><h3 class="font-heading font-semibold text-brand-ink">{{ f.title }}</h3><p class="mt-1 text-sm text-slate-600">{{ f.desc }}</p></div>
                </div>
            </div>
        </section>

        <!-- TESTIMONIALS -->
        <section class="bg-slate-50 py-24">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div data-animate class="mx-auto max-w-2xl text-center">
                    <span class="text-sm font-semibold uppercase tracking-wider text-brand-purple">Testimonials</span>
                    <h2 class="mt-2 font-heading text-4xl font-bold text-brand-ink">Loved by travellers</h2>
                </div>
                <div class="mt-14 grid gap-8 md:grid-cols-3" data-animate-group>
                    <figure v-for="t in testimonials" :key="t.name" data-animate class="rounded-3xl bg-white p-7 shadow-sm ring-1 ring-slate-100">
                        <div class="text-lg text-brand-magenta">★★★★★</div>
                        <blockquote class="mt-4 text-slate-700">“{{ t.quote }}”</blockquote>
                        <figcaption class="mt-6 flex items-center gap-3">
                            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-brand-gradient font-semibold text-white">{{ t.name.charAt(0) }}</div>
                            <div><div class="font-semibold text-brand-ink">{{ t.name }}</div><div class="text-xs text-slate-500">{{ t.role }}</div></div>
                        </figcaption>
                    </figure>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <div data-animate class="relative overflow-hidden rounded-[2.5rem] bg-brand-gradient px-8 py-16 text-center shadow-brand">
                <div class="dot-grid absolute inset-0 opacity-20"></div>
                <div class="relative">
                    <h2 class="font-heading text-3xl font-bold text-white sm:text-5xl">Ready to plan your next trip?</h2>
                    <p class="mx-auto mt-4 max-w-xl text-white/90">Start a visa application, request a ticket, or book a package — our team takes it from here.</p>
                    <div class="mt-9 flex flex-wrap justify-center gap-4">
                        <Link :href="route('register')" class="rounded-full bg-white px-8 py-4 font-semibold text-brand-ink transition hover:-translate-y-0.5">Get Started Free</Link>
                        <Link :href="route('contact')" class="rounded-full bg-white/15 px-8 py-4 font-semibold text-white ring-1 ring-white/40 transition hover:bg-white/25">Talk to an Expert</Link>
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
