<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const site = computed(() => usePage().props.site || {});
const form = useForm({ name: '', email: '', phone: '', subject: '', message: '' });

function submit() {
    form.post(route('contact.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}

const info = computed(() => [
    { icon: '📞', label: 'Call us', value: site.value.phone },
    { icon: '✉️', label: 'Email', value: site.value.email },
    { icon: '📍', label: 'Office', value: site.value.address },
    { icon: '🕐', label: 'Hours', value: site.value.hours },
]);
</script>

<template>
    <Head title="Contact Window Trip" />
    <PublicLayout>
        <section class="relative overflow-hidden brand-mesh">
            <div class="pointer-events-none absolute -top-24 right-0 h-72 w-72 animate-blob rounded-full bg-brand-blue/20 blur-3xl"></div>
            <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
                <nav class="text-sm text-slate-500"><Link href="/" class="hover:text-brand-purple">Home</Link> <span class="mx-2">/</span> <span class="text-brand-ink">Contact</span></nav>
                <h1 class="mt-4 font-heading text-4xl font-extrabold text-brand-ink sm:text-5xl">Let’s plan your <span class="text-gradient">next trip</span></h1>
                <p class="mt-4 max-w-2xl text-lg text-slate-600">Questions about a visa, ticket or package? Send us a message — our travel experts reply fast.</p>
            </div>
            <div class="gradient-rule"></div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <div class="grid gap-10 lg:grid-cols-3">
                <div class="space-y-6">
                    <div v-for="i in info" :key="i.label" class="flex items-start gap-4 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                        <span class="text-2xl">{{ i.icon }}</span>
                        <div>
                            <div class="text-xs uppercase tracking-wide text-slate-400">{{ i.label }}</div>
                            <div class="font-medium text-brand-ink">{{ i.value }}</div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm sm:p-8">
                        <form @submit.prevent="submit" class="space-y-5">
                            <div class="grid gap-5 sm:grid-cols-2">
                                <div>
                                    <label for="c-name" class="mb-1.5 block text-sm font-medium text-slate-700">Name</label>
                                    <input id="c-name" v-model="form.name" class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple" />
                                    <p v-if="form.errors.name" class="mt-1 text-xs text-red-500">{{ form.errors.name }}</p>
                                </div>
                                <div>
                                    <label for="c-email" class="mb-1.5 block text-sm font-medium text-slate-700">Email</label>
                                    <input id="c-email" v-model="form.email" type="email" class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple" />
                                    <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">{{ form.errors.email }}</p>
                                </div>
                            </div>
                            <div class="grid gap-5 sm:grid-cols-2">
                                <div>
                                    <label for="c-phone" class="mb-1.5 block text-sm font-medium text-slate-700">Phone <span class="text-slate-500">(optional)</span></label>
                                    <input id="c-phone" v-model="form.phone" class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple" />
                                </div>
                                <div>
                                    <label for="c-subject" class="mb-1.5 block text-sm font-medium text-slate-700">Subject <span class="text-slate-500">(optional)</span></label>
                                    <input id="c-subject" v-model="form.subject" class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple" />
                                </div>
                            </div>
                            <div>
                                <label for="c-msg" class="mb-1.5 block text-sm font-medium text-slate-700">Message</label>
                                <textarea id="c-msg" v-model="form.message" rows="5" class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple"></textarea>
                                <p v-if="form.errors.message" class="mt-1 text-xs text-red-500">{{ form.errors.message }}</p>
                            </div>
                            <button type="submit" :disabled="form.processing" class="rounded-full bg-brand-gradient px-7 py-3.5 font-semibold text-white shadow-brand transition hover:opacity-90 disabled:opacity-50">Send message</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
