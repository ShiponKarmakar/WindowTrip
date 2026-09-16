<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    profile: { type: Object, default: () => ({}) },
});

const info = useForm({
    name: props.profile.name || '',
    email: props.profile.email || '',
    phone: props.profile.phone || '',
});

const pass = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

function saveInfo() {
    info.patch(route('admin.profile.update'), { preserveScroll: true });
}
function savePassword() {
    pass.put(route('admin.profile.password'), {
        preserveScroll: true,
        onSuccess: () => pass.reset(),
    });
}
</script>

<template>
    <Head title="My Profile" />
    <AdminLayout>
        <template #title>My Profile</template>

        <div class="max-w-2xl space-y-6">
            <!-- Profile info -->
            <form @submit.prevent="saveInfo" class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <h3 class="font-heading font-semibold text-brand-ink">Profile information</h3>
                <p class="mt-1 text-sm text-slate-500">Update your name and contact details.</p>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div><label class="lbl">Full name</label><input v-model="info.name" class="inp" /><p v-if="info.errors.name" class="err">{{ info.errors.name }}</p></div>
                    <div><label class="lbl">Email</label><input v-model="info.email" type="email" class="inp" /><p v-if="info.errors.email" class="err">{{ info.errors.email }}</p></div>
                    <div><label class="lbl">Phone <span class="text-slate-400">(optional)</span></label><input v-model="info.phone" class="inp" /><p v-if="info.errors.phone" class="err">{{ info.errors.phone }}</p></div>
                </div>
                <div class="mt-5">
                    <button type="submit" :disabled="info.processing" class="rounded-full bg-brand-gradient px-6 py-2.5 text-sm font-semibold text-white shadow-brand hover:opacity-90 disabled:opacity-50">Save changes</button>
                </div>
            </form>

            <!-- Change password -->
            <form @submit.prevent="savePassword" class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <h3 class="font-heading font-semibold text-brand-ink">Change password</h3>
                <p class="mt-1 text-sm text-slate-500">Use a long, unique password to keep your account secure.</p>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2"><label class="lbl">Current password</label><input v-model="pass.current_password" type="password" class="inp" /><p v-if="pass.errors.current_password" class="err">{{ pass.errors.current_password }}</p></div>
                    <div><label class="lbl">New password</label><input v-model="pass.password" type="password" class="inp" /><p v-if="pass.errors.password" class="err">{{ pass.errors.password }}</p></div>
                    <div><label class="lbl">Confirm new password</label><input v-model="pass.password_confirmation" type="password" class="inp" /></div>
                </div>
                <div class="mt-5">
                    <button type="submit" :disabled="pass.processing" class="rounded-full bg-brand-gradient px-6 py-2.5 text-sm font-semibold text-white shadow-brand hover:opacity-90 disabled:opacity-50">Update password</button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<style scoped>
.lbl { @apply mb-1.5 block text-sm font-medium text-slate-700; }
.inp { @apply w-full rounded-xl border-slate-200 text-sm focus:border-brand-purple focus:ring-brand-purple; }
.err { @apply mt-1 text-xs text-red-500; }
</style>
