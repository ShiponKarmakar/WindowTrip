<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    applications: { type: Array, default: () => [] },
});

const page = usePage();
const user = computed(() => page.props.auth.user);
const isAdmin = computed(() => page.props.auth.isAdmin);

const steps = ['submitted', 'under_review', 'docs_required', 'approved'];
const statusLabel = (s) => s.replace('_', ' ');
const statusIndex = (s) => (s === 'rejected' ? -1 : steps.indexOf(s));
const statusBadge = (s) => ({
    submitted: 'bg-sky-50 text-sky-600',
    under_review: 'bg-amber-50 text-amber-600',
    docs_required: 'bg-orange-50 text-orange-600',
    approved: 'bg-emerald-50 text-emerald-600',
    rejected: 'bg-red-50 text-red-500',
}[s] || 'bg-slate-100 text-slate-500');
</script>

<template>
    <Head title="My Portal" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-heading text-xl font-semibold text-brand-ink">My Portal</h2>
                <Link v-if="isAdmin" href="/admin" class="rounded-full bg-brand-gradient px-4 py-2 text-sm font-semibold text-white">Admin Panel →</Link>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-6xl space-y-8 px-4 sm:px-6 lg:px-8">
                <!-- Welcome -->
                <div class="overflow-hidden rounded-3xl bg-brand-gradient p-8 text-white shadow-brand">
                    <h1 class="font-heading text-2xl font-bold">Welcome back, {{ user.name }} 👋</h1>
                    <p class="mt-1 text-white/85">Track your visa applications and start new ones any time.</p>
                    <div class="mt-6 flex flex-wrap gap-3">
                        <a href="/visa" class="rounded-full bg-white px-5 py-2.5 text-sm font-semibold text-brand-ink hover:-translate-y-0.5 transition">+ New visa application</a>
                        <a href="/packages" class="rounded-full bg-white/15 px-5 py-2.5 text-sm font-semibold text-white ring-1 ring-white/40 hover:bg-white/25 transition">Browse packages</a>
                        <Link :href="route('invoices.index')" class="rounded-full bg-white/15 px-5 py-2.5 text-sm font-semibold text-white ring-1 ring-white/40 hover:bg-white/25 transition">My invoices</Link>
                        <Link :href="route('my-tickets.index')" class="rounded-full bg-white/15 px-5 py-2.5 text-sm font-semibold text-white ring-1 ring-white/40 hover:bg-white/25 transition">My tickets</Link>
                    </div>
                </div>

                <!-- Applications -->
                <div>
                    <h2 class="font-heading text-lg font-semibold text-brand-ink">My visa applications</h2>

                    <div v-if="applications.length" class="mt-4 space-y-4">
                        <div v-for="a in applications" :key="a.reference" class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-2xl">{{ a.flag }}</span>
                                        <h3 class="font-heading font-semibold text-brand-ink capitalize">{{ a.country }} {{ a.visa_type }} visa</h3>
                                    </div>
                                    <p class="mt-1 font-mono text-xs text-brand-purple">{{ a.reference }} · {{ a.created }}</p>
                                </div>
                                <span class="rounded-full px-3 py-1 text-xs font-medium capitalize" :class="statusBadge(a.status)">{{ statusLabel(a.status) }}</span>
                            </div>

                            <!-- Progress tracker -->
                            <div v-if="a.status !== 'rejected'" class="mt-5 flex items-center">
                                <template v-for="(s, i) in steps" :key="s">
                                    <div class="flex flex-col items-center">
                                        <div class="flex h-7 w-7 items-center justify-center rounded-full text-xs font-semibold"
                                            :class="i <= statusIndex(a.status) ? 'bg-brand-gradient text-white' : 'bg-slate-100 text-slate-400'">
                                            <span v-if="i < statusIndex(a.status)">✓</span><span v-else>{{ i + 1 }}</span>
                                        </div>
                                        <span class="mt-1 hidden text-[10px] capitalize text-slate-500 sm:block">{{ statusLabel(s) }}</span>
                                    </div>
                                    <div v-if="i < steps.length - 1" class="mx-1 h-0.5 flex-1 rounded"
                                        :class="i < statusIndex(a.status) ? 'bg-brand-purple' : 'bg-slate-100'"></div>
                                </template>
                            </div>
                            <p v-else class="mt-4 rounded-xl bg-red-50 px-4 py-2 text-sm text-red-600">This application was not approved. Please contact us for next steps.</p>
                        </div>
                    </div>

                    <!-- Empty state -->
                    <div v-else class="mt-4 rounded-2xl border border-dashed border-slate-200 bg-white p-12 text-center">
                        <div class="text-4xl">🛂</div>
                        <h3 class="mt-3 font-heading font-semibold text-brand-ink">No applications yet</h3>
                        <p class="mt-1 text-sm text-slate-500">Start your first visa application in just a few steps.</p>
                        <a href="/visa" class="mt-5 inline-block rounded-full bg-brand-gradient px-6 py-3 text-sm font-semibold text-white shadow-brand hover:opacity-90">Start an application</a>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
