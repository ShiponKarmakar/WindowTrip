<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout title="Create your account" subtitle="Start your visa, ticket or package in minutes.">
        <Head title="Create account" />

        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <label for="name" class="mb-1.5 block text-sm font-medium text-slate-700">Full name</label>
                <input
                    id="name"
                    type="text"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="Your name"
                    class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple"
                />
                <p v-if="form.errors.name" class="mt-1 text-xs text-red-500">{{ form.errors.name }}</p>
            </div>

            <div>
                <label for="email" class="mb-1.5 block text-sm font-medium text-slate-700">Email</label>
                <input
                    id="email"
                    type="email"
                    v-model="form.email"
                    required
                    autocomplete="username"
                    placeholder="you@email.com"
                    class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple"
                />
                <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">{{ form.errors.email }}</p>
            </div>

            <div>
                <label for="phone" class="mb-1.5 block text-sm font-medium text-slate-700">Phone number</label>
                <input
                    id="phone"
                    type="tel"
                    v-model="form.phone"
                    required
                    autocomplete="tel"
                    placeholder="+880 1XXXXXXXXX"
                    class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple"
                />
                <p v-if="form.errors.phone" class="mt-1 text-xs text-red-500">{{ form.errors.phone }}</p>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="password" class="mb-1.5 block text-sm font-medium text-slate-700">Password</label>
                    <input
                        id="password"
                        type="password"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                        placeholder="••••••••"
                        class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple"
                    />
                    <p v-if="form.errors.password" class="mt-1 text-xs text-red-500">{{ form.errors.password }}</p>
                </div>
                <div>
                    <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-slate-700">Confirm</label>
                    <input
                        id="password_confirmation"
                        type="password"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="••••••••"
                        class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple"
                    />
                    <p v-if="form.errors.password_confirmation" class="mt-1 text-xs text-red-500">{{ form.errors.password_confirmation }}</p>
                </div>
            </div>

            <button
                type="submit"
                :disabled="form.processing"
                class="w-full rounded-xl bg-brand-gradient px-6 py-3.5 font-semibold text-white shadow-brand transition hover:opacity-90 disabled:opacity-50"
            >
                <span v-if="form.processing">Creating account…</span>
                <span v-else>Create account</span>
            </button>
        </form>

        <p class="mt-8 text-center text-sm text-slate-500">
            Already have an account?
            <Link :href="route('login')" class="font-semibold text-brand-purple hover:underline">Sign in</Link>
        </p>
    </GuestLayout>
</template>
