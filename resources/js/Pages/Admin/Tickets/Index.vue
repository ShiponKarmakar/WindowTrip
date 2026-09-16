<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';

const props = defineProps({
    tickets: Object,
    filters: Object,
    statuses: Array,
    summary: Object,
});

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');

function apply() {
    router.get('/admin/tickets',
        { search: search.value || undefined, status: status.value || undefined },
        { preserveState: true, replace: true });
}
watch(search, debounce(apply, 300));

const badge = (s) => ({
    draft: 'bg-slate-100 text-slate-500', issued: 'bg-emerald-50 text-emerald-600',
    cancelled: 'bg-red-50 text-red-500',
}[s] || 'bg-slate-100 text-slate-500');
</script>

<template>
    <Head title="Flight Tickets" />
    <AdminLayout>
        <template #title>Flight Tickets</template>

        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl bg-brand-gradient p-5 text-white shadow-brand">
                <div class="text-sm text-white/80">Total tickets</div>
                <div class="mt-1 font-heading text-2xl font-bold">{{ summary.total }}</div>
            </div>
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                <div class="text-sm text-slate-500">Issued</div>
                <div class="mt-1 font-heading text-2xl font-bold text-emerald-600">{{ summary.issued }}</div>
            </div>
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                <div class="text-sm text-slate-500">Drafts</div>
                <div class="mt-1 font-heading text-2xl font-bold text-brand-ink">{{ summary.draft }}</div>
            </div>
        </div>

        <div class="mt-5 flex flex-wrap items-center gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
            <input v-model="search" placeholder="Search number, PNR, client or email…" class="min-w-56 flex-1 rounded-xl border-slate-200 text-sm focus:border-brand-purple focus:ring-brand-purple" />
            <select v-model="status" @change="apply" class="rounded-xl border-slate-200 text-sm capitalize focus:border-brand-purple focus:ring-brand-purple">
                <option value="">All statuses</option>
                <option v-for="s in statuses" :key="s" :value="s">{{ s }}</option>
            </select>
            <Link :href="route('admin.tickets.create')" class="rounded-full bg-brand-gradient px-5 py-2.5 text-sm font-semibold text-white shadow-brand hover:opacity-90">+ New Ticket</Link>
        </div>

        <div class="mt-5 overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-400">
                    <tr>
                        <th class="px-5 py-3">Ticket</th><th class="px-5 py-3">PNR</th>
                        <th class="px-5 py-3">Client</th><th class="px-5 py-3">Route</th>
                        <th class="px-5 py-3">Status</th><th class="px-5 py-3">Issued</th><th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-for="t in tickets.data" :key="t.id" class="hover:bg-slate-50">
                        <td class="px-5 py-3 font-mono text-xs text-brand-purple">{{ t.number }}</td>
                        <td class="px-5 py-3 font-mono text-xs font-semibold text-brand-ink">{{ t.pnr }}</td>
                        <td class="px-5 py-3 font-medium text-brand-ink">{{ t.client_name }}</td>
                        <td class="px-5 py-3 text-slate-600">{{ t.route || '—' }}</td>
                        <td class="px-5 py-3"><span class="rounded-full px-2.5 py-1 text-xs font-medium capitalize" :class="badge(t.status)">{{ t.status }}</span></td>
                        <td class="px-5 py-3 text-slate-500">{{ t.issue_date }}</td>
                        <td class="px-5 py-3 text-right"><Link :href="route('admin.tickets.show', t.id)" class="text-sm font-semibold text-brand-purple hover:underline">Open →</Link></td>
                    </tr>
                    <tr v-if="!tickets.data.length"><td colspan="7" class="px-5 py-12 text-center text-slate-400">No tickets yet.</td></tr>
                </tbody>
            </table>
        </div>

        <div v-if="tickets.links.length > 3" class="mt-5 flex flex-wrap gap-1">
            <Link v-for="link in tickets.links" :key="link.label" :href="link.url || ''" v-html="link.label"
                class="rounded-lg px-3.5 py-2 text-sm"
                :class="[link.active ? 'bg-brand-gradient text-white' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50', !link.url && 'pointer-events-none opacity-40']" />
        </div>
    </AdminLayout>
</template>
