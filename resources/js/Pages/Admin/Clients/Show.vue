<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    client: Object,
    invoices: { type: Array, default: () => [] },
    tickets: { type: Array, default: () => [] },
});
const c = props.client;

const money = (n) => Number(n || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
const invBadge = (s) => ({
    draft: 'bg-slate-100 text-slate-500', sent: 'bg-sky-50 text-sky-600',
    partial: 'bg-amber-50 text-amber-600', paid: 'bg-emerald-50 text-emerald-600', cancelled: 'bg-red-50 text-red-500',
}[s] || 'bg-slate-100 text-slate-500');
const tkBadge = (s) => ({
    draft: 'bg-slate-100 text-slate-500', issued: 'bg-emerald-50 text-emerald-600', cancelled: 'bg-red-50 text-red-500',
}[s] || 'bg-slate-100 text-slate-500');

function loginAs() {
    if (confirm(`Log in to the customer portal as ${c.name}? You can return to admin anytime.`)) {
        router.post(route('admin.clients.login-as', c.id));
    }
}
function remove() {
    if (confirm(`Delete client ${c.name}? Their invoices and tickets are kept but unlinked. This cannot be undone.`)) {
        router.delete(route('admin.clients.destroy', c.id));
    }
}
</script>

<template>
    <Head :title="c.name" />
    <AdminLayout>
        <template #title>{{ c.name }}</template>

        <div class="flex flex-wrap items-center justify-between gap-3">
            <Link href="/admin/clients" class="text-sm font-medium text-slate-500 hover:text-brand-purple">← Back to clients</Link>
            <div class="flex flex-wrap gap-2">
                <Link :href="route('admin.invoices.create')" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-brand-ink hover:border-brand-purple/40">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M9 8h6M6 3h12a1 1 0 0 1 1 1v17l-3-2-2 2-2-2-2 2-2-2-3 2V4a1 1 0 0 1 1-1z"/></svg>
                    New invoice
                </Link>
                <Link :href="route('admin.tickets.create')" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-brand-ink hover:border-brand-purple/40">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 16v-2l-8-5V3.5a1.5 1.5 0 0 0-3 0V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L11 19v-5.5L21 16z"/></svg>
                    New ticket
                </Link>
                <button @click="loginAs" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-brand-ink hover:border-brand-purple/40">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3"/></svg>
                    Login as client
                </button>
                <Link :href="route('admin.clients.edit', c.id)" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-brand-ink hover:border-brand-purple/40">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 4H4v16h16v-7M18.5 2.5a2.1 2.1 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Edit
                </Link>
                <button @click="remove" class="inline-flex items-center gap-2 rounded-full border border-red-200 bg-white px-4 py-2 text-sm font-semibold text-red-500 hover:bg-red-50">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M9 7V5a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2m2 0v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7"/></svg>
                    Delete
                </button>
            </div>
        </div>

        <div class="mt-4 grid gap-6 lg:grid-cols-3">
            <!-- Profile -->
            <aside class="lg:col-span-1">
                <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-brand-gradient text-xl font-bold text-white">{{ c.name?.charAt(0) }}</div>
                    <h2 class="mt-3 font-heading text-lg font-bold text-brand-ink">{{ c.name }}</h2>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div><dt class="text-xs uppercase tracking-wide text-slate-400">Email</dt><dd class="text-brand-ink">{{ c.email }}</dd></div>
                        <div><dt class="text-xs uppercase tracking-wide text-slate-400">Phone</dt><dd class="text-brand-ink">{{ c.phone || '—' }}</dd></div>
                        <div><dt class="text-xs uppercase tracking-wide text-slate-400">Address</dt><dd class="text-brand-ink">{{ c.address || '—' }}</dd></div>
                        <div><dt class="text-xs uppercase tracking-wide text-slate-400">Client since</dt><dd class="text-brand-ink">{{ c.created }}</dd></div>
                    </dl>
                </div>
            </aside>

            <!-- Records -->
            <div class="space-y-6 lg:col-span-2">
                <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                    <h3 class="font-heading font-semibold text-brand-ink">Invoices ({{ invoices.length }})</h3>
                    <div v-if="invoices.length" class="mt-3 divide-y divide-slate-100 text-sm">
                        <Link v-for="i in invoices" :key="i.id" :href="route('admin.invoices.show', i.id)" class="flex items-center justify-between py-2.5 hover:bg-slate-50">
                            <div><span class="font-mono text-xs text-brand-purple">{{ i.number }}</span> <span class="text-slate-400">· {{ i.issue_date }}</span></div>
                            <div class="flex items-center gap-3">
                                <span>{{ i.currency }} {{ money(i.total) }}</span>
                                <span class="rounded-full px-2.5 py-0.5 text-xs font-medium capitalize" :class="invBadge(i.status)">{{ i.status }}</span>
                            </div>
                        </Link>
                    </div>
                    <p v-else class="mt-2 text-sm text-slate-400">No invoices yet.</p>
                </div>

                <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                    <h3 class="font-heading font-semibold text-brand-ink">Flight tickets ({{ tickets.length }})</h3>
                    <div v-if="tickets.length" class="mt-3 divide-y divide-slate-100 text-sm">
                        <Link v-for="t in tickets" :key="t.id" :href="route('admin.tickets.show', t.id)" class="flex items-center justify-between py-2.5 hover:bg-slate-50">
                            <div><span class="font-mono text-xs text-brand-purple">{{ t.number }}</span> <span class="text-slate-400">· {{ t.route || '—' }} · PNR {{ t.pnr }}</span></div>
                            <span class="rounded-full px-2.5 py-0.5 text-xs font-medium capitalize" :class="tkBadge(t.status)">{{ t.status }}</span>
                        </Link>
                    </div>
                    <p v-else class="mt-2 text-sm text-slate-400">No tickets yet.</p>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
