<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    leads: Object,
    filters: Object,
    statuses: Array,
});

const service = ref(props.filters.service || '');
const status = ref(props.filters.status || '');

function apply() {
    router.get('/admin/leads', { service: service.value || undefined, status: status.value || undefined },
        { preserveState: true, replace: true });
}

function setStatus(lead, value) {
    router.patch(route('admin.leads.status', lead.id), { status: value }, { preserveScroll: true });
}

const serviceBadge = (s) => ({
    general: 'bg-slate-100 text-slate-600',
    ticket: 'bg-sky-50 text-sky-600',
    package: 'bg-fuchsia-50 text-fuchsia-600',
    visa: 'bg-brand-50 text-brand-600',
}[s] || 'bg-slate-100 text-slate-500');

const statusBadge = (s) => ({
    new: 'bg-amber-50 text-amber-600',
    contacted: 'bg-sky-50 text-sky-600',
    converted: 'bg-emerald-50 text-emerald-600',
    closed: 'bg-slate-100 text-slate-500',
}[s] || 'bg-slate-100 text-slate-500');
</script>

<template>
    <Head title="Leads & Inquiries" />
    <AdminLayout>
        <template #title>Leads &amp; Inquiries</template>

        <div class="flex flex-wrap gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
            <select v-model="service" @change="apply" class="rounded-xl border-slate-200 text-sm capitalize focus:border-brand-purple focus:ring-brand-purple">
                <option value="">All services</option>
                <option value="general">General</option>
                <option value="ticket">Air ticket</option>
                <option value="package">Package</option>
            </select>
            <select v-model="status" @change="apply" class="rounded-xl border-slate-200 text-sm capitalize focus:border-brand-purple focus:ring-brand-purple">
                <option value="">All statuses</option>
                <option v-for="s in statuses" :key="s" :value="s">{{ s }}</option>
            </select>
        </div>

        <div class="mt-5 space-y-4">
            <div v-for="l in leads.data" :key="l.id" class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="rounded-full px-2.5 py-0.5 text-xs font-medium capitalize" :class="serviceBadge(l.service)">{{ l.service }}</span>
                            <h3 class="font-heading font-semibold text-brand-ink">{{ l.name }}</h3>
                        </div>
                        <p class="mt-1 text-sm text-slate-500">
                            <a :href="`mailto:${l.email}`" class="hover:text-brand-purple">{{ l.email }}</a>
                            <span v-if="l.phone"> · {{ l.phone }}</span>
                            <span> · {{ l.created }}</span>
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="rounded-full px-2.5 py-1 text-xs font-medium capitalize" :class="statusBadge(l.status)">{{ l.status }}</span>
                        <select :value="l.status" @change="setStatus(l, $event.target.value)"
                            class="rounded-lg border-slate-200 py-1 text-xs capitalize focus:border-brand-purple focus:ring-brand-purple">
                            <option v-for="s in statuses" :key="s" :value="s">{{ s }}</option>
                        </select>
                    </div>
                </div>

                <p v-if="l.subject" class="mt-3 text-sm font-medium text-brand-ink">{{ l.subject }}</p>
                <p v-if="l.message" class="mt-1 text-sm text-slate-600">{{ l.message }}</p>

                <!-- Ticket meta -->
                <div v-if="l.meta" class="mt-3 flex flex-wrap gap-2 text-xs">
                    <span v-for="(v, k) in l.meta" :key="k" class="rounded-full bg-slate-50 px-3 py-1 text-slate-600">
                        <span class="capitalize text-slate-400">{{ String(k).replace('_', ' ') }}:</span> {{ v }}
                    </span>
                </div>
            </div>

            <div v-if="!leads.data.length" class="rounded-2xl border border-dashed border-slate-200 bg-white p-12 text-center text-slate-400">
                No leads yet.
            </div>
        </div>

        <div v-if="leads.links.length > 3" class="mt-5 flex flex-wrap gap-1">
            <Link v-for="link in leads.links" :key="link.label" :href="link.url || ''" v-html="link.label"
                class="rounded-lg px-3.5 py-2 text-sm"
                :class="[link.active ? 'bg-brand-gradient text-white' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50', !link.url && 'pointer-events-none opacity-40']" />
        </div>
    </AdminLayout>
</template>
