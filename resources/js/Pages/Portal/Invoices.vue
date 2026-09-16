<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({ invoices: { type: Array, default: () => [] } });

const money = (n) => Number(n || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
const badge = (s) => ({
    sent: 'bg-sky-50 text-sky-600', partial: 'bg-amber-50 text-amber-600',
    paid: 'bg-emerald-50 text-emerald-600', cancelled: 'bg-red-50 text-red-500',
}[s] || 'bg-slate-100 text-slate-500');
</script>

<template>
    <Head title="My Invoices" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-heading text-xl font-semibold text-brand-ink">My Invoices</h2>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <div v-if="invoices.length" class="space-y-4">
                    <div v-for="i in invoices" :key="i.id" class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                        <div>
                            <div class="font-mono text-sm text-brand-purple">{{ i.number }}</div>
                            <div class="mt-1 text-sm text-slate-500">Issued {{ i.issue_date }}<span v-if="i.due_date"> · Due {{ i.due_date }}</span></div>
                        </div>
                        <div class="text-right">
                            <div class="font-heading text-lg font-bold text-brand-ink">{{ i.currency }} {{ money(i.total) }}</div>
                            <div v-if="i.balance > 0" class="text-xs text-amber-600">Balance {{ money(i.balance) }}</div>
                        </div>
                        <span class="rounded-full px-3 py-1 text-xs font-medium capitalize" :class="badge(i.status)">{{ i.status }}</span>
                        <a :href="route('invoices.pdf', i.id)" class="rounded-full bg-brand-gradient px-5 py-2.5 text-sm font-semibold text-white hover:opacity-90">Download PDF</a>
                    </div>
                </div>
                <div v-else class="rounded-2xl border border-dashed border-slate-200 bg-white p-12 text-center">
                    <div class="text-4xl">🧾</div>
                    <h3 class="mt-3 font-heading font-semibold text-brand-ink">No invoices yet</h3>
                    <p class="mt-1 text-sm text-slate-500">When we issue an invoice for your service, it’ll appear here.</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
