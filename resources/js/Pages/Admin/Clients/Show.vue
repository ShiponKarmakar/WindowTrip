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
                <Link :href="route('admin.invoices.create')" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-brand-ink hover:border-brand-purple/40">🧾 New invoice</Link>
                <Link :href="route('admin.tickets.create')" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-brand-ink hover:border-brand-purple/40">✈️ New ticket</Link>
                <Link :href="route('admin.clients.edit', c.id)" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-brand-ink hover:border-brand-purple/40">✏️ Edit</Link>
                <button @click="remove" class="rounded-full border border-red-200 bg-white px-4 py-2 text-sm font-semibold text-red-500 hover:bg-red-50">Delete</button>
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
