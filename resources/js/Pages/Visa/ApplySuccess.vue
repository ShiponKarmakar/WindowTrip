<script setup>
import { Head } from '@inertiajs/vue3';

defineProps({
    reference: String,
    country: Object,
});
</script>

<template>
    <Head title="Application submitted" />

    <div class="flex min-h-screen flex-col items-center justify-center bg-slate-50 px-4">
        <div class="w-full max-w-md rounded-3xl border border-slate-100 bg-white p-8 text-center shadow-brand">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-brand-gradient text-3xl text-white">
                ✓
            </div>
            <h1 class="mt-6 font-heading text-2xl font-bold text-brand-ink">Application submitted!</h1>
            <p class="mt-2 text-slate-600">
                Your {{ country.flag }} {{ country.name }} visa application has been received.
                Our team will review it and contact you shortly.
            </p>

            <div class="mt-6 rounded-2xl bg-slate-50 p-4">
                <div class="text-xs uppercase tracking-wide text-slate-400">Reference number</div>
                <div class="mt-1 font-heading text-xl font-bold text-brand-purple">{{ reference }}</div>
            </div>

            <p class="mt-4 text-xs text-slate-400">Save this reference — you’ll use it to track your application.</p>

            <p class="mt-4 rounded-xl bg-amber-50 px-4 py-3 text-left text-xs leading-relaxed text-amber-800">
                <strong>Note:</strong> We process and submit your application — we do not issue visas and cannot guarantee
                approval. The final decision is made by the embassy/consulate.
            </p>

            <div class="mt-8 flex flex-col gap-3">
                <a href="/" class="rounded-full bg-brand-gradient px-6 py-3 font-semibold text-white hover:opacity-90 transition">
                    Back to Home
                </a>
                <a :href="route('visa.index')" class="rounded-full bg-white px-6 py-3 font-semibold text-brand-ink ring-1 ring-slate-200 hover:ring-brand-purple/40 transition">
                    Browse other visas
                </a>
            </div>
        </div>
    </div>
</template>
