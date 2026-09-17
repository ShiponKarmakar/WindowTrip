<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    application: Object,
    statuses: Array,
});

const form = useForm({ status: props.application.status });

function updateStatus() {
    form.patch(route('admin.applications.status', props.application.id), { preserveScroll: true });
}

const statusBadge = (s) => ({
    submitted: 'bg-sky-50 text-sky-600',
    under_review: 'bg-amber-50 text-amber-600',
    docs_required: 'bg-orange-50 text-orange-600',
    approved: 'bg-emerald-50 text-emerald-600',
    rejected: 'bg-red-50 text-red-500',
}[s] || 'bg-slate-100 text-slate-500');

const field = (label, value) => ({ label, value: value || '—' });
const a = props.application;
const personal = [
    field('Full name (passport)', a.full_name),
    field('Date of birth', a.date_of_birth),
    field('Gender', a.gender),
    field('Nationality', a.nationality),
];
const passport = [
    field('Passport number', a.passport_number),
    field('Passport expiry', a.passport_expiry),
];
const trip = [
    field('Visa type', a.visa_type),
    field('Travellers', a.travellers),
    field('Intended travel', a.travel_date),
];
const contact = [
    field('Email', a.email),
    field('Phone', a.phone),
];
</script>

<template>
    <Head :title="`Application ${application.reference}`" />
    <AdminLayout>
        <template #title>Application Review</template>

        <div class="flex items-center justify-between">
            <Link href="/admin/applications" class="text-sm font-medium text-slate-500 hover:text-brand-purple">← Back to applications</Link>
            <Link :href="route('admin.applications.edit', application.id)" class="inline-flex items-center gap-2 rounded-full bg-brand-gradient px-5 py-2.5 text-sm font-semibold text-white shadow-brand hover:opacity-90">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 4H4v16h16v-7M18.5 2.5a2.1 2.1 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Edit information
            </Link>
        </div>

        <div class="mt-4 grid gap-6 lg:grid-cols-3">
            <!-- Details -->
            <div class="space-y-6 lg:col-span-2">
                <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-3xl">{{ application.flag }}</span>
                                <h2 class="font-heading text-xl font-bold text-brand-ink">{{ application.country }} {{ application.visa_type }} visa</h2>
                            </div>
                            <p class="mt-1 font-mono text-sm text-brand-purple">{{ application.reference }}</p>
                        </div>
                        <span class="rounded-full px-3 py-1 text-xs font-medium capitalize" :class="statusBadge(application.status)">{{ application.status.replace('_', ' ') }}</span>
                    </div>
                    <p class="mt-3 text-xs text-slate-400">Submitted {{ application.created }}</p>
                </div>

                <div v-for="(group, gi) in [{t:'Trip', f:trip},{t:'Personal', f:personal},{t:'Passport', f:passport},{t:'Contact', f:contact}]" :key="gi"
                    class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                    <h3 class="font-heading font-semibold text-brand-ink">{{ group.t }}</h3>
                    <dl class="mt-4 grid gap-4 sm:grid-cols-2">
                        <div v-for="f in group.f" :key="f.label">
                            <dt class="text-xs uppercase tracking-wide text-slate-400">{{ f.label }}</dt>
                            <dd class="mt-0.5 font-medium capitalize text-brand-ink">{{ f.value }}</dd>
                        </div>
                    </dl>
                </div>

                <div v-if="application.notes" class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                    <h3 class="font-heading font-semibold text-brand-ink">Applicant notes</h3>
                    <p class="mt-2 text-slate-600">{{ application.notes }}</p>
                </div>
            </div>

            <!-- Actions sidebar -->
            <aside class="space-y-6">
                <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                    <h3 class="font-heading font-semibold text-brand-ink">Update status</h3>
                    <select v-model="form.status" class="mt-3 w-full rounded-xl border-slate-200 text-sm capitalize focus:border-brand-purple focus:ring-brand-purple">
                        <option v-for="s in statuses" :key="s" :value="s">{{ s.replace('_', ' ') }}</option>
                    </select>
                    <button @click="updateStatus" :disabled="form.processing || form.status === application.status"
                        class="mt-3 w-full rounded-xl bg-brand-gradient px-5 py-3 text-sm font-semibold text-white shadow-brand transition hover:opacity-90 disabled:opacity-40">
                        Save status
                    </button>
                </div>

                <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                    <h3 class="font-heading font-semibold text-brand-ink">Documents</h3>
                    <div class="mt-3 space-y-2">
                        <a v-if="application.has_passport_scan" :href="`/admin/applications/${application.id}/document/passport`" target="_blank"
                            class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3 text-sm hover:bg-slate-100">
                            <span class="inline-flex items-center gap-2"><svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 3H7a1 1 0 0 0-1 1v16a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V8zM14 3v5h5"/></svg>Passport scan</span><span class="text-brand-purple">Open →</span>
                        </a>
                        <a v-if="application.has_photo" :href="`/admin/applications/${application.id}/document/photo`" target="_blank"
                            class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3 text-sm hover:bg-slate-100">
                            <span class="inline-flex items-center gap-2"><svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="8.5" cy="10" r="1.5"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 16l-5-5L5 20"/></svg>Photo</span><span class="text-brand-purple">Open →</span>
                        </a>
                        <p v-if="!application.has_passport_scan && !application.has_photo" class="text-sm text-slate-400">No documents uploaded.</p>
                    </div>
                </div>

                <Link :href="route('admin.applications.edit', application.id)" class="flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-3 text-center text-sm font-semibold text-brand-ink hover:border-brand-purple/40">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 4H4v16h16v-7M18.5 2.5a2.1 2.1 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Edit application
                </Link>

                <Link :href="route('admin.invoices.create', { application: application.id })" class="flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-3 text-center text-sm font-semibold text-brand-ink hover:border-brand-purple/40">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M9 8h6M6 3h12a1 1 0 0 1 1 1v17l-3-2-2 2-2-2-2 2-2-2-3 2V4a1 1 0 0 1 1-1z"/></svg>
                    Create invoice
                </Link>

                <Link :href="route('admin.tickets.create', { application: application.id })" class="flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-3 text-center text-sm font-semibold text-brand-ink hover:border-brand-purple/40">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 16v-2l-8-5V3.5a1.5 1.5 0 0 0-3 0V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L11 19v-5.5L21 16z"/></svg>
                    Create flight ticket
                </Link>

                <Link :href="route('admin.applications.compose', application.id)" class="flex w-full items-center justify-center gap-2 rounded-2xl bg-brand-gradient px-5 py-3 text-sm font-semibold text-white shadow-brand hover:opacity-90">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l9 6 9-6M5 5h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2z"/></svg>
                    Compose email
                </Link>
                <a :href="`mailto:${application.email}`" class="block text-center text-xs text-slate-400 hover:text-brand-purple">or open in mail app</a>
            </aside>
        </div>
    </AdminLayout>
</template>
