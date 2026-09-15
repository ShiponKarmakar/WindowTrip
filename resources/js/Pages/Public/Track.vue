<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    result: { type: Object, default: null },
    searched: { type: Boolean, default: false },
    old: { type: Object, default: () => ({}) },
});

const form = useForm({
    reference: props.old?.reference || '',
    email: props.old?.email || '',
});

function submit() {
    form.post(route('track.check'), { preserveScroll: true });
}

const steps = [
    { key: 'submitted', label: 'Submitted', desc: 'We received your application.' },
    { key: 'under_review', label: 'Under Review', desc: 'Our team is checking your file.' },
    { key: 'docs_required', label: 'Processing', desc: 'Documents verified & lodged.' },
    { key: 'approved', label: 'Approved', desc: 'Your visa is ready.' },
];
const order = { submitted: 0, under_review: 1, docs_required: 2, approved: 3 };
const currentIndex = (s) => (s === 'rejected' ? -1 : order[s] ?? 0);
const badge = (s) => ({
    submitted: 'bg-sky-50 text-sky-600', under_review: 'bg-amber-50 text-amber-600',
    docs_required: 'bg-orange-50 text-orange-600', approved: 'bg-emerald-50 text-emerald-600',
    rejected: 'bg-red-50 text-red-500',
}[s] || 'bg-slate-100 text-slate-500');
</script>

<template>
    <Head title="Track Your Application — Window Trip" />
    <PublicLayout>
        <section class="relative overflow-hidden brand-mesh">
            <div class="pointer-events-none absolute -top-24 right-0 h-72 w-72 animate-blob rounded-full bg-brand-blue/20 blur-3xl"></div>
            <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
                <nav class="text-sm text-slate-500"><Link href="/" class="hover:text-brand-purple">Home</Link> <span class="mx-2">/</span> <span class="text-brand-ink">Track Application</span></nav>
                <h1 class="mt-4 font-heading text-4xl font-extrabold text-brand-ink sm:text-5xl">Track your <span class="text-gradient">application</span></h1>
                <p class="mt-4 max-w-2xl text-lg text-slate-600">Enter your reference number and email to see exactly where your application stands.</p>
            </div>
            <div class="gradient-rule"></div>
        </section>

        <section class="mx-auto max-w-3xl px-4 py-14 sm:px-6 lg:px-8">
            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm sm:p-8">
                <form @submit.prevent="submit" class="space-y-5">
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="reference" class="mb-1.5 block text-sm font-medium text-slate-700">Reference / Application ID</label>
                            <input id="reference" v-model="form.reference" placeholder="WT-XXXXXXXX" class="w-full rounded-xl border-slate-200 uppercase focus:border-brand-purple focus:ring-brand-purple" />
                            <p v-if="form.errors.reference" class="mt-1 text-xs text-red-500">{{ form.errors.reference }}</p>
                        </div>
                        <div>
                            <label for="temail" class="mb-1.5 block text-sm font-medium text-slate-700">Email used on application</label>
                            <input id="temail" v-model="form.email" type="email" placeholder="you@email.com" class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple" />
                            <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">{{ form.errors.email }}</p>
                        </div>
                    </div>
                    <div v-if="searched && !result" class="rounded-xl bg-red-50 px-4 py-3 text-sm font-medium text-red-600">
                        No application found for that reference and email. Please check and try again.
                    </div>
                    <button type="submit" :disabled="form.processing" class="inline-flex items-center gap-2 rounded-full bg-brand-gradient px-7 py-3.5 font-semibold text-white shadow-brand transition hover:opacity-90 disabled:opacity-50">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7" /><path stroke-linecap="round" d="M21 21l-4.3-4.3" /></svg>
                        Track Application
                    </button>
                </form>
            </div>

            <div v-if="result" class="mt-8 rounded-2xl border border-slate-100 bg-white p-6 shadow-sm sm:p-8">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-3xl">{{ result.flag }}</span>
                            <h2 class="font-heading text-xl font-bold capitalize text-brand-ink">{{ result.country }} {{ result.visa_type }} visa</h2>
                        </div>
                        <p class="mt-1 font-mono text-sm text-brand-purple">{{ result.reference }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ result.name }} · Submitted {{ result.submitted }}</p>
                    </div>
                    <span class="rounded-full px-3 py-1 text-xs font-medium capitalize" :class="badge(result.status)">{{ result.status.replace('_', ' ') }}</span>
                </div>

                <div v-if="result.status !== 'rejected'" class="mt-8">
                    <div v-for="(step, i) in steps" :key="step.key" class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full text-sm font-semibold" :class="i <= currentIndex(result.status) ? 'bg-brand-gradient text-white' : 'bg-slate-100 text-slate-400'">
                                <span v-if="i < currentIndex(result.status)">✓</span><span v-else>{{ i + 1 }}</span>
                            </div>
                            <div v-if="i < steps.length - 1" class="my-1 h-10 w-0.5" :class="i < currentIndex(result.status) ? 'bg-brand-purple' : 'bg-slate-100'"></div>
                        </div>
                        <div class="pb-2">
                            <div class="font-heading font-semibold" :class="i <= currentIndex(result.status) ? 'text-brand-ink' : 'text-slate-400'">{{ step.label }}</div>
                            <div class="text-sm text-slate-500">{{ step.desc }}</div>
                            <div v-if="i === currentIndex(result.status)" class="mt-1 inline-block rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-medium text-brand-600">Current · updated {{ result.updated }}</div>
                        </div>
                    </div>
                </div>
                <div v-else class="mt-6 rounded-xl bg-red-50 px-5 py-4 text-sm text-red-600">
                    Unfortunately this application was not approved. Please <Link :href="route('contact')" class="font-semibold underline">contact us</Link> to discuss next steps.
                </div>

                <div class="mt-8 flex flex-wrap gap-3 border-t border-slate-100 pt-6">
                    <Link :href="route('contact')" class="rounded-full bg-white px-5 py-2.5 text-sm font-semibold text-brand-ink ring-1 ring-slate-200 transition hover:ring-brand-purple/40">Need help?</Link>
                    <Link :href="route('visa.index')" class="rounded-full bg-brand-gradient px-5 py-2.5 text-sm font-semibold text-white transition hover:opacity-90">Start another application</Link>
                </div>
            </div>

            <p class="mt-6 text-center text-sm text-slate-500">Lost your reference? <Link :href="route('contact')" class="font-medium text-brand-purple hover:underline">Contact us</Link> and we’ll find it for you.</p>
        </section>
    </PublicLayout>
</template>
