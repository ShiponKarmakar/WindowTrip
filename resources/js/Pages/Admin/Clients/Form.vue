<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    client: { type: Object, default: null },
});

const isEdit = !!props.client;
const c = props.client || {};

const form = useForm({
    name: c.name || '',
    email: c.email || '',
    phone: c.phone || '',
    address: c.address || '',
});

function submit() {
    const opts = { preserveScroll: true };
    isEdit ? form.put(route('admin.clients.update', props.client.id), opts) : form.post(route('admin.clients.store'), opts);
}
</script>

<template>
    <Head :title="isEdit ? `Edit ${c.name}` : 'New Client'" />
    <AdminLayout>
        <template #title>{{ isEdit ? `Edit ${c.name}` : 'New Client' }}</template>

        <Link href="/admin/clients" class="text-sm font-medium text-slate-500 hover:text-brand-purple">← Back to clients</Link>

        <form @submit.prevent="submit" class="mt-4 max-w-2xl space-y-6">
            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <h3 class="font-heading font-semibold text-brand-ink">Client details</h3>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div><label class="lbl">Full name</label><input v-model="form.name" class="inp" /><p v-if="form.errors.name" class="err">{{ form.errors.name }}</p></div>
                    <div><label class="lbl">Email</label><input v-model="form.email" type="email" class="inp" /><p v-if="form.errors.email" class="err">{{ form.errors.email }}</p></div>
                    <div><label class="lbl">Phone</label><input v-model="form.phone" class="inp" /><p v-if="form.errors.phone" class="err">{{ form.errors.phone }}</p></div>
                    <div><label class="lbl">Address <span class="text-slate-400">(optional)</span></label><input v-model="form.address" class="inp" /><p v-if="form.errors.address" class="err">{{ form.errors.address }}</p></div>
                </div>
                <p v-if="!isEdit" class="mt-4 rounded-xl bg-brand-50/60 px-4 py-3 text-xs text-slate-500">
                    A portal account is created for this client. They don’t get a password by email — if they want to view their invoices and tickets online, they can use “Forgot password” on the login page to set one.
                </p>
            </div>

            <div v-if="form.hasErrors" class="rounded-xl bg-red-50 px-4 py-3 text-sm text-red-600">Please fix the highlighted fields before saving.</div>
            <div class="flex items-center gap-3">
                <button type="submit" :disabled="form.processing" class="rounded-full bg-brand-gradient px-7 py-3 text-sm font-semibold text-white shadow-brand hover:opacity-90 disabled:opacity-50">{{ isEdit ? 'Save client' : 'Add client' }}</button>
                <Link href="/admin/clients" class="text-sm text-slate-500 hover:text-brand-ink">Cancel</Link>
            </div>
        </form>
    </AdminLayout>
</template>

<style scoped>
.lbl { @apply mb-1.5 block text-sm font-medium text-slate-700; }
.inp { @apply w-full rounded-xl border-slate-200 text-sm focus:border-brand-purple focus:ring-brand-purple; }
.err { @apply mt-1 text-xs text-red-500; }
</style>
