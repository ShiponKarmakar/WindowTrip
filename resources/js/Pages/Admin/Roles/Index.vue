<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    roles: { type: Array, default: () => [] },
    features: { type: Array, default: () => [] },
});

const labelFor = (key) => props.features.find((f) => f.key === key)?.label || key;

function remove(r) {
    if (r.locked) return;
    if (confirm(`Delete the "${r.name}" role? This cannot be undone.`)) {
        router.delete(route('admin.roles.destroy', r.id), { preserveScroll: true });
    }
}
</script>

<template>
    <Head title="Roles" />
    <AdminLayout>
        <template #title>Staff & Roles</template>

        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex gap-2">
                <Link href="/admin/staff" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Staff</Link>
                <Link href="/admin/roles" class="rounded-full bg-brand-gradient px-4 py-2 text-sm font-semibold text-white shadow-brand">Roles</Link>
            </div>
            <Link :href="route('admin.roles.create')" class="rounded-full bg-brand-gradient px-5 py-2.5 text-sm font-semibold text-white shadow-brand hover:opacity-90">+ New Role</Link>
        </div>

        <div class="mt-5 grid gap-4 md:grid-cols-2">
            <div v-for="r in roles" :key="r.id" class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="font-heading text-lg font-semibold capitalize text-brand-ink">{{ r.name }}</h3>
                        <p class="mt-0.5 text-xs text-slate-400">{{ r.users_count }} staff · {{ r.permission_count }} features</p>
                    </div>
                    <span v-if="r.locked" class="rounded-full bg-brand-50 px-2.5 py-1 text-xs font-medium text-brand-600">Full access</span>
                </div>

                <div class="mt-4 flex flex-wrap gap-1.5">
                    <span v-for="p in r.permissions" :key="p" class="rounded-lg bg-slate-100 px-2 py-1 text-xs text-slate-600">{{ labelFor(p) }}</span>
                    <span v-if="!r.permissions.length" class="text-xs text-slate-400">No features assigned.</span>
                </div>

                <div v-if="!r.locked" class="mt-5 flex gap-3 border-t border-slate-100 pt-4">
                    <Link :href="route('admin.roles.edit', r.id)" class="text-xs font-semibold text-brand-purple hover:underline">Edit</Link>
                    <button @click="remove(r)" class="text-xs font-semibold text-red-400 hover:text-red-600">Delete</button>
                </div>
                <p v-else class="mt-5 border-t border-slate-100 pt-4 text-xs text-slate-400">The administrator role always has every feature and can't be changed.</p>
            </div>
        </div>
    </AdminLayout>
</template>
