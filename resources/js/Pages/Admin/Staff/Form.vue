<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    staff: { type: Object, default: null },
    roles: { type: Array, default: () => [] },
});

const isEdit = !!props.staff;
const s = props.staff || {};

const form = useForm({
    name: s.name || '',
    email: s.email || '',
    phone: s.phone || '',
    role: s.role || (props.roles[0] || ''),
    password: '',
    password_confirmation: '',
});

function submit() {
    const opts = { preserveScroll: true };
    isEdit ? form.put(route('admin.staff.update', props.staff.id), opts) : form.post(route('admin.staff.store'), opts);
}
</script>

<template>
    <Head :title="isEdit ? `Edit ${s.name}` : 'New Staff'" />
    <AdminLayout>
        <template #title>{{ isEdit ? `Edit ${s.name}` : 'New Staff' }}</template>

        <Link href="/admin/staff" class="text-sm font-medium text-slate-500 hover:text-brand-purple">← Back to staff</Link>

        <form @submit.prevent="submit" class="mt-4 max-w-2xl space-y-6">
            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <h3 class="font-heading font-semibold text-brand-ink">Staff details</h3>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div><label class="lbl">Full name</label><input v-model="form.name" class="inp" /><p v-if="form.errors.name" class="err">{{ form.errors.name }}</p></div>
                    <div><label class="lbl">Email</label><input v-model="form.email" type="email" class="inp" /><p v-if="form.errors.email" class="err">{{ form.errors.email }}</p></div>
                    <div><label class="lbl">Phone <span class="text-slate-400">(optional)</span></label><input v-model="form.phone" class="inp" /></div>
                    <div>
                        <label class="lbl">Role</label>
                        <select v-model="form.role" class="inp capitalize"><option v-for="r in roles" :key="r" :value="r">{{ r }}</option></select>
                        <p v-if="form.errors.role" class="err">{{ form.errors.role }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <h3 class="font-heading font-semibold text-brand-ink">{{ isEdit ? 'Change password' : 'Password' }}</h3>
                <p v-if="isEdit" class="mt-1 text-sm text-slate-500">Leave blank to keep the current password.</p>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div><label class="lbl">Password</label><input v-model="form.password" type="password" class="inp" /><p v-if="form.errors.password" class="err">{{ form.errors.password }}</p></div>
                    <div><label class="lbl">Confirm password</label><input v-model="form.password_confirmation" type="password" class="inp" /></div>
                </div>
            </div>

            <div v-if="form.hasErrors" class="rounded-xl bg-red-50 px-4 py-3 text-sm text-red-600">Please fix the highlighted fields.</div>
            <div class="flex items-center gap-3">
                <button type="submit" :disabled="form.processing" class="rounded-full bg-brand-gradient px-7 py-3 text-sm font-semibold text-white shadow-brand hover:opacity-90 disabled:opacity-50">{{ isEdit ? 'Save' : 'Add staff' }}</button>
                <Link href="/admin/staff" class="text-sm text-slate-500 hover:text-brand-ink">Cancel</Link>
            </div>
        </form>
    </AdminLayout>
</template>

<style scoped>
.lbl { @apply mb-1.5 block text-sm font-medium text-slate-700; }
.inp { @apply w-full rounded-xl border-slate-200 text-sm focus:border-brand-purple focus:ring-brand-purple; }
.err { @apply mt-1 text-xs text-red-500; }
</style>
