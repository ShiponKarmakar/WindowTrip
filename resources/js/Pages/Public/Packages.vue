<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({ packages: { type: Array, default: () => [] } });
</script>

<template>
    <Head title="Tour Packages — Curated Trips | Window Trip" />
    <PublicLayout>
        <section class="relative overflow-hidden brand-mesh">
            <div class="pointer-events-none absolute -top-24 right-0 h-72 w-72 animate-blob rounded-full bg-brand-blue/20 blur-3xl"></div>
            <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
                <nav class="text-sm text-slate-500"><Link href="/" class="hover:text-brand-purple">Home</Link> <span class="mx-2">/</span> <span class="text-brand-ink">Tour Packages</span></nav>
                <h1 class="mt-4 font-heading text-4xl font-extrabold text-brand-ink sm:text-5xl">Tour <span class="text-gradient">Packages</span></h1>
                <p class="mt-4 max-w-2xl text-lg text-slate-600">Handpicked trips with flights, stays, transfers and experiences — all arranged for you.</p>
            </div>
            <div class="gradient-rule"></div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3" data-animate-group>
                <div v-for="p in packages" :key="p.slug" data-animate class="group flex flex-col overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 transition hover:shadow-brand">
                    <div class="relative h-48" :style="{ backgroundColor: p.color || '#139dd5' }">
                        <div class="absolute inset-0 dot-grid opacity-30"></div>
                        <span v-if="p.tag" class="absolute left-4 top-4 rounded-full bg-white/90 px-3 py-1 text-xs font-semibold text-brand-ink">{{ p.tag }}</span>
                        <span class="absolute right-4 top-4 rounded-full bg-black/20 px-3 py-1 text-xs font-medium text-white">{{ p.destination }}</span>
                        <span class="absolute bottom-4 right-4 text-5xl text-white/30">✈︎</span>
                    </div>
                    <div class="flex flex-1 flex-col p-6">
                        <div class="flex items-center justify-between">
                            <h3 class="font-heading text-lg font-semibold text-brand-ink">{{ p.title }}</h3>
                            <span class="text-xs text-slate-500">{{ p.nights }}</span>
                        </div>
                        <ul class="mt-4 space-y-1.5 text-sm text-slate-600">
                            <li v-for="inc in p.includes" :key="inc" class="flex items-center gap-2"><span class="text-emerald-500">✓</span> {{ inc }}</li>
                        </ul>
                        <div class="mt-6 flex items-end justify-between border-t border-slate-100 pt-4">
                            <div>
                                <div class="text-xs text-slate-500">From</div>
                                <div class="font-heading text-xl font-bold text-brand-ink">৳{{ p.price }}</div>
                            </div>
                            <Link :href="route('packages.book', p.slug)" class="rounded-full bg-brand-gradient px-5 py-2.5 text-sm font-semibold text-white transition hover:opacity-90">Book Now</Link>
                        </div>
                    </div>
                </div>
            </div>

            <div data-animate class="mt-14 rounded-3xl bg-brand-gradient px-8 py-12 text-center shadow-brand">
                <h2 class="font-heading text-2xl font-bold text-white sm:text-3xl">Want a custom itinerary?</h2>
                <p class="mt-3 text-white/90">Tell us your dream destination and budget — we’ll build a package just for you.</p>
                <Link :href="route('contact')" class="mt-6 inline-block rounded-full bg-white px-7 py-3.5 font-semibold text-brand-ink transition hover:-translate-y-0.5">Plan my trip</Link>
            </div>
        </section>
    </PublicLayout>
</template>
