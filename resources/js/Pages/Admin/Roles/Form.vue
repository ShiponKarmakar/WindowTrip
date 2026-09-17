<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    role: { type: Object, default: null },
    features: { type: Array, default: () => [] },
});

const isEdit = !!props.role;
const r = props.role || {};

const form = useForm({
    name: r.name || '',
    permissions: r.permissions ? [...r.permissions] : [],
});

function toggle(key) {
    const i = form.permissions.indexOf(key);
    i === -1 ? form.permissions.push(key) : form.permissions.splice(i, 1);
}
function selectAll() { form.permissions = props.features.map((f) => f.key); }
function clearAll() { form.permissions = []; }

function submit() {
    const opts = { preserveScroll: true };
    isEdit ? form.put(route('admin.roles.update', props.role.id), opts) : form.post(route('admin.roles.store'), opts);
}
</script>

<template>
    <Head :title="isEdit ? `Edit ${r.name}` : 'New Role'" />
    <AdminLayout>
        <template #title>{{ isEdit ? `Edit role: ${r.name}` : 'New Role' }}</template>

        <Link href="/admin/roles" class="text-sm font-medium text-slate-500 hover:text-brand-purple">← Back to roles</Link>

        <form @submit.prevent="submit" class="mt-4 max-w-2xl space-y-6">
            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <label class="lbl">Role name</label>
                <input v-model="form.name" class="inp" placeholder="e.g. Accountant, Ticketing agent" />
                <p v-if="form.errors.name" class="err">{{ form.errors.name }}</p>
            </div>

            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h3 class="font-heading font-semibold text-brand-ink">Feature access</h3>
                    <div class="flex gap-3 text-xs font-semibold">
                        <button type="button" @click="selectAll" class="text-brand-purple hover:underline">Select all</button>
                        <button type="button" @click="clearAll" class="text-slate-400 hover:text-slate-600">Clear</button>
                    </div>
                </div>
                <p class="mt-1 text-sm text-slate-500">Choose which sections this role can access.</p>
                <p v-if="form.errors.permissions" class="err">{{ form.errors.permissions }}</p>

                <div class="mt-4 grid gap-2 sm:grid-cols-2">
                    <label v-for="f in features" :key="f.key" class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-100 px-4 py-3 hover:bg-slate-50" :class="form.permissions.includes(f.key) && 'border-brand-purple/40 bg-brand-50/40'">
                        <input type="checkbox" :checked="form.permissions.includes(f.key)" @change="toggle(f.key)" class="rounded border-slate-300 text-brand-purple focus:ring-brand-purple" />
                        <span class="text-sm font-medium text-brand-ink">{{ f.label }}</span>
                    </label>
                </div>
            </div>

            <div v-if="form.hasErrors" class="rounded-xl bg-red-50 px-4 py-3 text-sm text-red-600">Please fix the highlighted fields.</div>
            <div class="flex items-center gap-3">
                <button type="submit" :disabled="form.processing" class="rounded-full bg-brand-gradient px-7 py-3 text-sm font-semibold text-white shadow-brand hover:opacity-90 disabled:opacity-50">{{ isEdit ? 'Save role' : 'Create role' }}</button>
                <Link href="/admin/roles" class="text-sm text-slate-500 hover:text-brand-ink">Cancel</Link>
            </div>
        </form>
    </AdminLayout>
</template>

<style scoped>
.lbl { @apply mb-1.5 block text-sm font-medium text-slate-700; }
.inp { @apply w-full rounded-xl border-slate-200 text-sm focus:border-brand-purple focus:ring-brand-purple; }
.err { @apply mt-1 text-xs text-red-500; }
</style>
