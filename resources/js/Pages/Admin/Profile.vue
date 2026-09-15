<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    profile: Object,
});

const info = useForm({
    name: props.profile.name,
    email: props.profile.email,
});

const pwd = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

function saveInfo() {
    info.patch(route('admin.profile.update'), { preserveScroll: true });
}
function savePassword() {
    pwd.put(route('admin.profile.password'), {
        preserveScroll: true,
        onSuccess: () => pwd.reset(),
    });
}
</script>

<template>
    <Head title="My Profile" />
    <AdminLayout>
        <template #title>My Profile</template>

        <div class="mx-auto max-w-3xl space-y-6">
            <!-- Identity card -->
            <div class="flex items-center gap-4 rounded-2xl bg-brand-gradient p-6 text-white shadow-brand">
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-white/20 text-2xl font-bold">
                    {{ profile.name?.charAt(0) }}
                </div>
                <div>
                    <h2 class="font-heading text-xl font-bold">{{ profile.name }}</h2>
                    <p class="text-sm text-white/80">{{ profile.email }}</p>
                    <div class="mt-1 flex flex-wrap gap-1">
                        <span v-for="r in profile.roles" :key="r" class="rounded-full bg-white/20 px-2.5 py-0.5 text-xs font-medium capitalize">{{ r }}</span>
                        <span class="rounded-full bg-white/20 px-2.5 py-0.5 text-xs">Joined {{ profile.joined }}</span>
                    </div>
                </div>
            </div>

            <!-- Profile info -->
            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <h3 class="font-heading font-semibold text-brand-ink">Profile information</h3>
                <p class="mt-1 text-sm text-slate-500">Update your name and email address.</p>
                <form @submit.prevent="saveInfo" class="mt-5 space-y-4">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Name</label>
                            <input v-model="info.name" type="text" class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple" />
                            <p v-if="info.errors.name" class="mt-1 text-xs text-red-500">{{ info.errors.name }}</p>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Email</label>
                            <input v-model="info.email" type="email" class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple" />
                            <p v-if="info.errors.email" class="mt-1 text-xs text-red-500">{{ info.errors.email }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="submit" :disabled="info.processing" class="rounded-full bg-brand-gradient px-6 py-2.5 text-sm font-semibold text-white shadow-brand hover:opacity-90 disabled:opacity-50">Save changes</button>
                        <span v-if="info.recentlySuccessful" class="text-sm text-emerald-600">Saved ✓</span>
                    </div>
                </form>
            </div>

            <!-- Password -->
            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <h3 class="font-heading font-semibold text-brand-ink">Update password</h3>
                <p class="mt-1 text-sm text-slate-500">Use a long, random password to stay secure.</p>
                <form @submit.prevent="savePassword" class="mt-5 space-y-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700">Current password</label>
                        <input v-model="pwd.current_password" type="password" class="w-full max-w-sm rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple" />
                        <p v-if="pwd.errors.current_password" class="mt-1 text-xs text-red-500">{{ pwd.errors.current_password }}</p>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">New password</label>
                            <input v-model="pwd.password" type="password" class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple" />
                            <p v-if="pwd.errors.password" class="mt-1 text-xs text-red-500">{{ pwd.errors.password }}</p>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Confirm new password</label>
                            <input v-model="pwd.password_confirmation" type="password" class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple" />
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="submit" :disabled="pwd.processing" class="rounded-full bg-brand-gradient px-6 py-2.5 text-sm font-semibold text-white shadow-brand hover:opacity-90 disabled:opacity-50">Update password</button>
                        <span v-if="pwd.recentlySuccessful" class="text-sm text-emerald-600">Updated ✓</span>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
