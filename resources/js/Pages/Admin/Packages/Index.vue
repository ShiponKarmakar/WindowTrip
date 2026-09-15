<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({ packages: Array });

function remove(p) {
    if (confirm(`Delete package "${p.title}"?`)) {
        router.delete(route('admin.packages.destroy', p.id), { preserveScroll: true });
    }
}
</script>

<template>
    <Head title="Tour Packages" />
    <AdminLayout>
        <template #title>Tour Packages</template>

        <div class="mb-5 flex items-center justify-between">
            <p class="text-sm text-slate-500">{{ packages.length }} packages · shown on the public packages page.</p>
            <Link :href="route('admin.packages.create')" class="rounded-full bg-brand-gradient px-5 py-2.5 text-sm font-semibold text-white shadow-brand hover:opacity-90">+ Add Package</Link>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div v-for="p in packages" :key="p.id" class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
                <div class="relative h-24" :style="{ backgroundColor: p.color || '#139dd5' }">
                    <span class="absolute left-3 top-3 rounded-full bg-white/90 px-2.5 py-0.5 text-xs font-semibold text-brand-ink">{{ p.tag }}</span>
                    <span v-if="!p.active" class="absolute right-3 top-3 rounded-full bg-black/40 px-2.5 py-0.5 text-xs font-medium text-white">Hidden</span>
                </div>
                <div class="p-4">
                    <h3 class="font-heading font-semibold text-brand-ink">{{ p.title }}</h3>
                    <p class="text-xs text-slate-500">{{ p.destination }} · {{ p.nights }}</p>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="font-heading font-bold text-brand-ink">৳{{ p.price }}</span>
                        <div>
                            <Link :href="route('admin.packages.edit', p.id)" class="text-sm font-semibold text-brand-purple hover:underline">Edit</Link>
                            <button @click="remove(p)" class="ml-3 text-sm font-medium text-red-500 hover:underline">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="!packages.length" class="col-span-full rounded-2xl border border-dashed border-slate-200 p-12 text-center text-slate-400">No packages yet.</div>
        </div>
    </AdminLayout>
</template>
