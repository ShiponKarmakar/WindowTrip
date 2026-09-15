<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    stats: Object,
    byCountry: Array,
    recent: Array,
});

const cards = computed(() => [
    { label: 'Total applications', value: props.stats.total, tone: 'bg-brand-gradient text-white' },
    { label: 'New (submitted)', value: props.stats.submitted, tone: 'bg-white text-brand-ink' },
    { label: 'Under review', value: props.stats.under_review, tone: 'bg-white text-brand-ink' },
    { label: 'Docs required', value: props.stats.docs_required, tone: 'bg-white text-brand-ink' },
    { label: 'Approved', value: props.stats.approved, tone: 'bg-white text-emerald-600' },
    { label: 'Rejected', value: props.stats.rejected, tone: 'bg-white text-red-500' },
]);

const maxCountry = computed(() => Math.max(1, ...props.byCountry.map((c) => c.total)));

const statusBadge = (s) => ({
    submitted: 'bg-sky-50 text-sky-600',
    under_review: 'bg-amber-50 text-amber-600',
    docs_required: 'bg-orange-50 text-orange-600',
    approved: 'bg-emerald-50 text-emerald-600',
    rejected: 'bg-red-50 text-red-500',
}[s] || 'bg-slate-100 text-slate-500');
</script>

<template>
    <Head title="Admin Dashboard" />
    <AdminLayout>
        <template #title>Dashboard</template>

        <!-- KPI cards -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
            <div v-for="c in cards" :key="c.label" class="rounded-2xl border border-slate-100 p-5 shadow-sm" :class="c.tone">
                <div class="text-3xl font-bold font-heading">{{ c.value }}</div>
                <div class="mt-1 text-sm opacity-80">{{ c.label }}</div>
            </div>
        </div>

        <div class="mt-6 grid gap-6 lg:grid-cols-2">
            <!-- Applications by country -->
            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <h2 class="font-heading font-semibold text-brand-ink">Applications by country</h2>
                <div v-if="byCountry.length" class="mt-5 space-y-3">
                    <div v-for="c in byCountry" :key="c.country">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-600">{{ c.flag }} {{ c.country }}</span>
                            <span class="font-semibold text-brand-ink">{{ c.total }}</span>
                        </div>
                        <div class="mt-1 h-2 overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full bg-brand-gradient" :style="{ width: (c.total / maxCountry * 100) + '%' }"></div>
                        </div>
                    </div>
                </div>
                <p v-else class="mt-4 text-sm text-slate-400">No applications yet.</p>
            </div>

            <!-- Recent -->
            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="font-heading font-semibold text-brand-ink">Recent applications</h2>
                    <Link href="/admin/applications" class="text-sm font-medium text-brand-purple hover:underline">View all →</Link>
                </div>
                <div v-if="recent.length" class="mt-4 divide-y divide-slate-100">
                    <Link v-for="r in recent" :key="r.id" :href="`/admin/applications/${r.id}`" class="flex items-center justify-between py-3 hover:bg-slate-50">
                        <div>
                            <div class="text-sm font-medium text-brand-ink">{{ r.name }}</div>
                            <div class="text-xs text-slate-400">{{ r.reference }} · {{ r.country }} · {{ r.created }}</div>
                        </div>
                        <span class="rounded-full px-2.5 py-1 text-xs font-medium capitalize" :class="statusBadge(r.status)">{{ r.status.replace('_', ' ') }}</span>
                    </Link>
                </div>
                <p v-else class="mt-4 text-sm text-slate-400">No applications yet.</p>
            </div>
        </div>
    </AdminLayout>
</template>
