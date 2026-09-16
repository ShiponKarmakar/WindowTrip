<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({ tickets: { type: Array, default: () => [] } });

const badge = (s) => ({
    issued: 'bg-emerald-50 text-emerald-600', cancelled: 'bg-red-50 text-red-500',
}[s] || 'bg-slate-100 text-slate-500');
</script>

<template>
    <Head title="My Tickets" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-heading text-xl font-semibold text-brand-ink">My Flight Tickets</h2>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <div v-if="tickets.length" class="space-y-4">
                    <div v-for="t in tickets" :key="t.id" class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                        <div>
                            <div class="font-mono text-sm text-brand-purple">{{ t.number }}</div>
                            <div class="mt-1 font-heading font-semibold text-brand-ink">{{ t.route || '—' }}</div>
                            <div class="mt-1 text-sm text-slate-500">PNR <span class="font-mono font-semibold text-brand-ink">{{ t.pnr }}</span><span v-if="t.airline"> · {{ t.airline }}</span> · Issued {{ t.issue_date }}</div>
                        </div>
                        <span class="rounded-full px-3 py-1 text-xs font-medium capitalize" :class="badge(t.status)">{{ t.status }}</span>
                        <a :href="route('my-tickets.pdf', t.id)" class="rounded-full bg-brand-gradient px-5 py-2.5 text-sm font-semibold text-white hover:opacity-90">Download e-ticket</a>
                    </div>
                </div>
                <div v-else class="rounded-2xl border border-dashed border-slate-200 bg-white p-12 text-center">
                    <div class="text-4xl">✈️</div>
                    <h3 class="mt-3 font-heading font-semibold text-brand-ink">No tickets yet</h3>
                    <p class="mt-1 text-sm text-slate-500">When we issue a flight ticket for your booking, it’ll appear here.</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
