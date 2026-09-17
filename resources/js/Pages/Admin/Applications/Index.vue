<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';

const props = defineProps({
    applications: Object,
    filters: Object,
    statuses: Array,
    countries: Array,
});

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');
const country = ref(props.filters.country || '');

function apply() {
    router.get('/admin/applications',
        { search: search.value || undefined, status: status.value || undefined, country: country.value || undefined },
        { preserveState: true, replace: true });
}

watch(search, debounce(apply, 300));

const statusBadge = (s) => ({
    submitted: 'bg-sky-50 text-sky-600',
    under_review: 'bg-amber-50 text-amber-600',
    docs_required: 'bg-orange-50 text-orange-600',
    approved: 'bg-emerald-50 text-emerald-600',
    rejected: 'bg-red-50 text-red-500',
}[s] || 'bg-slate-100 text-slate-500');
</script>

<template>
    <Head title="Visa Applications" />
    <AdminLayout>
        <template #title>Visa Applications</template>

        <!-- Filters -->
        <div class="flex flex-wrap items-center gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
            <input v-model="search" type="text" placeholder="Search name, reference or email…"
                class="min-w-56 flex-1 rounded-xl border-slate-200 text-sm focus:border-brand-purple focus:ring-brand-purple" />
            <select v-model="status" @change="apply" class="rounded-xl border-slate-200 text-sm capitalize focus:border-brand-purple focus:ring-brand-purple">
                <option value="">All statuses</option>
                <option v-for="s in statuses" :key="s" :value="s">{{ s.replace('_', ' ') }}</option>
            </select>
            <select v-model="country" @change="apply" class="rounded-xl border-slate-200 text-sm focus:border-brand-purple focus:ring-brand-purple">
                <option value="">All countries</option>
                <option v-for="c in countries" :key="c.slug" :value="c.slug">{{ c.name }}</option>
            </select>
        </div>

        <!-- Table -->
        <div class="mt-5 overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-400">
                    <tr>
                        <th class="px-5 py-3">Reference</th>
                        <th class="px-5 py-3">Applicant</th>
                        <th class="px-5 py-3">Country</th>
                        <th class="px-5 py-3">Type</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-for="a in applications.data" :key="a.id" class="hover:bg-slate-50">
                        <td class="px-5 py-3"><Link :href="`/admin/applications/${a.id}`" class="font-mono text-xs font-semibold text-brand-purple hover:underline">{{ a.reference }}</Link></td>
                        <td class="px-5 py-3">
                            <div class="font-medium text-brand-ink">{{ a.name }}</div>
                            <div class="text-xs text-slate-400">{{ a.email }}</div>
                        </td>
                        <td class="px-5 py-3">{{ a.flag }} {{ a.country }}</td>
                        <td class="px-5 py-3 capitalize text-slate-600">{{ a.visa_type }}</td>
                        <td class="px-5 py-3">
                            <span class="rounded-full px-2.5 py-1 text-xs font-medium capitalize" :class="statusBadge(a.status)">{{ a.status.replace('_', ' ') }}</span>
                        </td>
                        <td class="px-5 py-3 text-slate-500">{{ a.created }}</td>
                    </tr>
                    <tr v-if="!applications.data.length">
                        <td colspan="6" class="px-5 py-12 text-center text-slate-400">No applications match your filters.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div v-if="applications.links.length > 3" class="mt-5 flex flex-wrap gap-1">
            <Link v-for="link in applications.links" :key="link.label"
                :href="link.url || ''"
                v-html="link.label"
                class="rounded-lg px-3.5 py-2 text-sm"
                :class="[
                    link.active ? 'bg-brand-gradient text-white' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50',
                    !link.url && 'pointer-events-none opacity-40',
                ]" />
        </div>
    </AdminLayout>
</template>
