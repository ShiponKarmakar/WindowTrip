<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';

const props = defineProps({
    staff: { type: Array, default: () => [] },
    filters: Object,
});

const search = ref(props.filters.search || '');
watch(search, debounce(() => {
    router.get('/admin/staff', { search: search.value || undefined }, { preserveState: true, replace: true });
}, 300));

const roleBadge = (r) => ({
    admin: 'bg-brand-50 text-brand-600', agent: 'bg-sky-50 text-sky-600',
}[r] || 'bg-slate-100 text-slate-600');

function remove(s) {
    if (s.is_self) return;
    if (confirm(`Remove staff member ${s.name}? This cannot be undone.`)) {
        router.delete(route('admin.staff.destroy', s.id), { preserveScroll: true });
    }
}
</script>

<template>
    <Head title="Staff" />
    <AdminLayout>
        <template #title>Staff & Roles</template>

        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex gap-2">
                <Link href="/admin/staff" class="rounded-full bg-brand-gradient px-4 py-2 text-sm font-semibold text-white shadow-brand">Staff</Link>
                <Link href="/admin/roles" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Roles</Link>
            </div>
            <Link :href="route('admin.staff.create')" class="rounded-full bg-brand-gradient px-5 py-2.5 text-sm font-semibold text-white shadow-brand hover:opacity-90">+ New Staff</Link>
        </div>

        <div class="mt-5 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
            <input v-model="search" placeholder="Search name or email…" class="w-full rounded-xl border-slate-200 text-sm focus:border-brand-purple focus:ring-brand-purple" />
        </div>

        <div class="mt-5 overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-400">
                    <tr><th class="px-5 py-3">Name</th><th class="px-5 py-3">Email</th><th class="px-5 py-3">Phone</th><th class="px-5 py-3">Role</th><th class="px-5 py-3 text-right"></th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-for="s in staff" :key="s.id" class="hover:bg-slate-50">
                        <td class="px-5 py-3 font-medium text-brand-ink">{{ s.name }} <span v-if="s.is_self" class="text-xs text-slate-400">(you)</span></td>
                        <td class="px-5 py-3 text-slate-600">{{ s.email }}</td>
                        <td class="px-5 py-3 text-slate-600">{{ s.phone || '—' }}</td>
                        <td class="px-5 py-3"><span class="rounded-full px-2.5 py-1 text-xs font-medium capitalize" :class="roleBadge(s.role)">{{ s.role || '—' }}</span></td>
                        <td class="px-5 py-3 text-right">
                            <Link :href="route('admin.staff.edit', s.id)" class="text-xs font-semibold text-brand-purple hover:underline">Edit</Link>
                            <button v-if="!s.is_self" @click="remove(s)" class="ml-3 text-xs font-semibold text-red-400 hover:text-red-600">Delete</button>
                        </td>
                    </tr>
                    <tr v-if="!staff.length"><td colspan="5" class="px-5 py-12 text-center text-slate-400">No staff yet.</td></tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
