<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';

const props = defineProps({
    invoices: Object,
    filters: Object,
    statuses: Array,
    summary: Object,
});

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');

function apply() {
    router.get('/admin/invoices',
        { search: search.value || undefined, status: status.value || undefined },
        { preserveState: true, replace: true });
}
watch(search, debounce(apply, 300));

const money = (n) => Number(n).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
const badge = (s) => ({
    draft: 'bg-slate-100 text-slate-500', sent: 'bg-sky-50 text-sky-600',
    partial: 'bg-amber-50 text-amber-600', paid: 'bg-emerald-50 text-emerald-600',
    cancelled: 'bg-red-50 text-red-500',
}[s] || 'bg-slate-100 text-slate-500');
</script>

<template>
    <Head title="Invoices" />
    <AdminLayout>
        <template #title>Invoices</template>

        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl bg-brand-gradient p-5 text-white shadow-brand">
                <div class="text-sm text-white/80">Outstanding</div>
                <div class="mt-1 font-heading text-2xl font-bold">৳{{ money(summary.outstanding) }}</div>
            </div>
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                <div class="text-sm text-slate-500">Total received</div>
                <div class="mt-1 font-heading text-2xl font-bold text-emerald-600">৳{{ money(summary.paid) }}</div>
            </div>
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                <div class="text-sm text-slate-500">Invoices</div>
                <div class="mt-1 font-heading text-2xl font-bold text-brand-ink">{{ summary.count }}</div>
            </div>
        </div>

        <div class="mt-5 flex flex-wrap items-center gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
            <input v-model="search" placeholder="Search number, client or email…" class="min-w-56 flex-1 rounded-xl border-slate-200 text-sm focus:border-brand-purple focus:ring-brand-purple" />
            <select v-model="status" @change="apply" class="rounded-xl border-slate-200 text-sm capitalize focus:border-brand-purple focus:ring-brand-purple">
                <option value="">All statuses</option>
                <option v-for="s in statuses" :key="s" :value="s">{{ s }}</option>
            </select>
            <Link :href="route('admin.invoices.create')" class="rounded-full bg-brand-gradient px-5 py-2.5 text-sm font-semibold text-white shadow-brand hover:opacity-90">+ New Invoice</Link>
        </div>

        <div class="mt-5 overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-400">
                    <tr>
                        <th class="px-5 py-3">Invoice</th><th class="px-5 py-3">Client</th>
                        <th class="px-5 py-3">Total</th><th class="px-5 py-3">Balance</th>
                        <th class="px-5 py-3">Status</th><th class="px-5 py-3">Issued</th><th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-for="i in invoices.data" :key="i.id" class="hover:bg-slate-50">
                        <td class="px-5 py-3 font-mono text-xs text-brand-purple">{{ i.number }}</td>
                        <td class="px-5 py-3 font-medium text-brand-ink">{{ i.client_name }}</td>
                        <td class="px-5 py-3">{{ i.currency }} {{ money(i.total) }}</td>
                        <td class="px-5 py-3" :class="i.balance > 0 ? 'text-amber-600' : 'text-slate-400'">{{ money(i.balance) }}</td>
                        <td class="px-5 py-3"><span class="rounded-full px-2.5 py-1 text-xs font-medium capitalize" :class="badge(i.status)">{{ i.status }}</span></td>
                        <td class="px-5 py-3 text-slate-500">{{ i.issue_date }}</td>
                        <td class="px-5 py-3 text-right"><Link :href="route('admin.invoices.show', i.id)" class="text-sm font-semibold text-brand-purple hover:underline">Open →</Link></td>
                    </tr>
                    <tr v-if="!invoices.data.length"><td colspan="7" class="px-5 py-12 text-center text-slate-400">No invoices yet.</td></tr>
                </tbody>
            </table>
        </div>

        <div v-if="invoices.links.length > 3" class="mt-5 flex flex-wrap gap-1">
            <Link v-for="link in invoices.links" :key="link.label" :href="link.url || ''" v-html="link.label"
                class="rounded-lg px-3.5 py-2 text-sm"
                :class="[link.active ? 'bg-brand-gradient text-white' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50', !link.url && 'pointer-events-none opacity-40']" />
        </div>
    </AdminLayout>
</template>
