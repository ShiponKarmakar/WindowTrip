<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';

const props = defineProps({
    clients: Object,
    filters: Object,
    summary: Object,
});

const search = ref(props.filters.search || '');

function apply() {
    router.get('/admin/clients',
        { search: search.value || undefined },
        { preserveState: true, replace: true });
}
watch(search, debounce(apply, 300));
</script>

<template>
    <Head title="Clients" />
    <AdminLayout>
        <template #title>Clients</template>

        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="rounded-2xl bg-brand-gradient px-6 py-3 text-white shadow-brand">
                <span class="text-sm text-white/80">Total clients: </span><span class="font-heading text-xl font-bold">{{ summary.total }}</span>
            </div>
            <Link :href="route('admin.clients.create')" class="rounded-full bg-brand-gradient px-5 py-2.5 text-sm font-semibold text-white shadow-brand hover:opacity-90">+ New Client</Link>
        </div>

        <div class="mt-5 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
            <input v-model="search" placeholder="Search name, email or phone…" class="w-full rounded-xl border-slate-200 text-sm focus:border-brand-purple focus:ring-brand-purple" />
        </div>

        <div class="mt-5 overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-400">
                    <tr>
                        <th class="px-5 py-3">Name</th><th class="px-5 py-3">Email</th>
                        <th class="px-5 py-3">Phone</th><th class="px-5 py-3 text-center">Invoices</th>
                        <th class="px-5 py-3 text-center">Tickets</th><th class="px-5 py-3">Added</th><th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-for="c in clients.data" :key="c.id" class="hover:bg-slate-50">
                        <td class="px-5 py-3 font-medium text-brand-ink">{{ c.name }}</td>
                        <td class="px-5 py-3 text-slate-600">{{ c.email }}</td>
                        <td class="px-5 py-3 text-slate-600">{{ c.phone || '—' }}</td>
                        <td class="px-5 py-3 text-center text-slate-600">{{ c.invoices_count }}</td>
                        <td class="px-5 py-3 text-center text-slate-600">{{ c.tickets_count }}</td>
                        <td class="px-5 py-3 text-slate-500">{{ c.created }}</td>
                        <td class="px-5 py-3 text-right"><Link :href="route('admin.clients.show', c.id)" class="text-sm font-semibold text-brand-purple hover:underline">Open →</Link></td>
                    </tr>
                    <tr v-if="!clients.data.length"><td colspan="7" class="px-5 py-12 text-center text-slate-400">No clients yet. Add your first client to select them on invoices and tickets.</td></tr>
                </tbody>
            </table>
        </div>

        <div v-if="clients.links.length > 3" class="mt-5 flex flex-wrap gap-1">
            <Link v-for="link in clients.links" :key="link.label" :href="link.url || ''" v-html="link.label"
                class="rounded-lg px-3.5 py-2 text-sm"
                :class="[link.active ? 'bg-brand-gradient text-white' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50', !link.url && 'pointer-events-none opacity-40']" />
        </div>
    </AdminLayout>
</template>
