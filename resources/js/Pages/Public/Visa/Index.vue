<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({ countries: { type: Array, default: () => [] } });
</script>

<template>
    <Head title="Tourist Visa Processing — India, USA, Europe & More | Window Trip" />
    <PublicLayout>
        <section class="relative overflow-hidden brand-mesh">
            <div class="pointer-events-none absolute -top-24 right-0 h-72 w-72 animate-blob rounded-full bg-brand-magenta/20 blur-3xl"></div>
            <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
                <nav class="text-sm text-slate-500"><Link href="/" class="hover:text-brand-purple">Home</Link> <span class="mx-2">/</span> <span class="text-brand-ink">Visa Processing</span></nav>
                <h1 class="mt-4 font-heading text-4xl font-extrabold text-brand-ink sm:text-5xl">Tourist <span class="text-gradient">Visa Processing</span></h1>
                <p class="mt-4 max-w-2xl text-lg text-slate-600">Pick a destination to see the exact requirements, documents, processing time and fees. We prepare, submit and track your application end to end.</p>
                <p class="mt-4 inline-block rounded-xl bg-amber-50 px-4 py-2.5 text-sm text-amber-800">⚠️ We provide visa <strong>processing assistance only</strong> — approval is decided by the embassy and is not guaranteed.</p>
            </div>
            <div class="gradient-rule"></div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3" data-animate-group>
                <Link v-for="c in countries" :key="c.slug" :href="route('visa.show', c.slug)" data-animate class="group rounded-2xl bg-white p-7 ring-1 ring-slate-100 transition hover:-translate-y-1 hover:shadow-brand hover:ring-brand-purple/30">
                    <div class="flex items-start justify-between">
                        <span class="text-5xl">{{ c.flag }}</span>
                        <span class="rounded-full bg-brand-50 px-3 py-1 text-xs font-medium text-brand-600">{{ c.processing }}</span>
                    </div>
                    <h2 class="mt-5 font-heading text-xl font-semibold text-brand-ink transition group-hover:text-brand-purple">{{ c.name }}</h2>
                    <p class="text-sm text-slate-500">{{ c.subtitle }}</p>
                    <p class="mt-3 line-clamp-2 text-sm text-slate-600">{{ c.overview }}</p>
                    <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4">
                        <span class="text-sm text-slate-500">From <span class="font-semibold text-brand-ink">৳{{ c.fee_from }}</span></span>
                        <span class="text-sm font-semibold text-brand-purple">View requirements →</span>
                    </div>
                </Link>
            </div>

            <div data-animate class="mt-16 rounded-3xl bg-brand-gradient px-8 py-12 text-center shadow-brand">
                <h2 class="font-heading text-2xl font-bold text-white sm:text-3xl">Not sure which visa you need?</h2>
                <p class="mt-3 text-white/90">Tell us your travel plan and we’ll guide you to the right one.</p>
                <Link :href="route('contact')" class="mt-6 inline-block rounded-full bg-white px-7 py-3.5 font-semibold text-brand-ink transition hover:-translate-y-0.5">Talk to a Visa Expert</Link>
            </div>
        </section>
    </PublicLayout>
</template>
