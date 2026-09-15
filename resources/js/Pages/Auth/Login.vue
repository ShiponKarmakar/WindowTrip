<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout title="Welcome back" subtitle="Sign in to manage your applications and bookings.">
        <Head title="Sign in" />

        <div v-if="status" class="mb-4 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <label for="email" class="mb-1.5 block text-sm font-medium text-slate-700">Email</label>
                <input
                    id="email"
                    type="email"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="you@email.com"
                    class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple"
                />
                <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">{{ form.errors.email }}</p>
            </div>

            <div>
                <div class="mb-1.5 flex items-center justify-between">
                    <label for="password" class="text-sm font-medium text-slate-700">Password</label>
                    <Link v-if="canResetPassword" :href="route('password.request')" class="text-xs font-medium text-brand-purple hover:underline">
                        Forgot password?
                    </Link>
                </div>
                <input
                    id="password"
                    type="password"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                    class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple"
                />
                <p v-if="form.errors.password" class="mt-1 text-xs text-red-500">{{ form.errors.password }}</p>
            </div>

            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" v-model="form.remember" class="rounded border-slate-300 text-brand-purple focus:ring-brand-purple" />
                Remember me
            </label>

            <button
                type="submit"
                :disabled="form.processing"
                class="w-full rounded-xl bg-brand-gradient px-6 py-3.5 font-semibold text-white shadow-brand transition hover:opacity-90 disabled:opacity-50"
            >
                <span v-if="form.processing">Signing in…</span>
                <span v-else>Sign in</span>
            </button>
        </form>

        <p class="mt-8 text-center text-sm text-slate-500">
            New to Window Trip?
            <Link :href="route('register')" class="font-semibold text-brand-purple hover:underline">Create an account</Link>
        </p>
    </GuestLayout>
</template>
