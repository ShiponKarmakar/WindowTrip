<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({ countries: Array });

function remove(c) {
    if (confirm(`Delete ${c.name} visa destination?`)) {
        router.delete(route('admin.visas.destroy', c.id), { preserveScroll: true });
    }
}
</script>

<template>
    <Head title="Visa Destinations" />
    <AdminLayout>
        <template #title>Visa Destinations</template>

        <div class="mb-5 flex items-center justify-between">
            <p class="text-sm text-slate-500">{{ countries.length }} destinations · shown on the public visa pages.</p>
            <Link :href="route('admin.visas.create')" class="rounded-full bg-brand-gradient px-5 py-2.5 text-sm font-semibold text-white shadow-brand hover:opacity-90">+ Add Visa</Link>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-400">
                    <tr>
                        <th class="px-5 py-3">Country</th>
                        <th class="px-5 py-3">Type</th>
                        <th class="px-5 py-3">Processing</th>
                        <th class="px-5 py-3">Fee from</th>
                        <th class="px-5 py-3">Docs</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-for="c in countries" :key="c.id" class="hover:bg-slate-50">
                        <td class="px-5 py-3"><span class="text-xl">{{ c.flag }}</span> <span class="font-medium text-brand-ink">{{ c.name }}</span></td>
                        <td class="px-5 py-3 text-slate-600">{{ c.subtitle }}</td>
                        <td class="px-5 py-3 text-slate-600">{{ c.processing }}</td>
                        <td class="px-5 py-3 text-slate-600">৳{{ c.fee_from }}</td>
                        <td class="px-5 py-3 text-slate-600">{{ c.documents }}</td>
                        <td class="px-5 py-3">
                            <span class="rounded-full px-2.5 py-1 text-xs font-medium" :class="c.active ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-500'">{{ c.active ? 'Active' : 'Hidden' }}</span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <Link :href="route('admin.visas.edit', c.id)" class="text-sm font-semibold text-brand-purple hover:underline">Edit</Link>
                            <button @click="remove(c)" class="ml-3 text-sm font-medium text-red-500 hover:underline">Delete</button>
                        </td>
                    </tr>
                    <tr v-if="!countries.length"><td colspan="7" class="px-5 py-12 text-center text-slate-400">No visa destinations yet.</td></tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
