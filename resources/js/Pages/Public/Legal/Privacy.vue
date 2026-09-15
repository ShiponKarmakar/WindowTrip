<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const site = computed(() => usePage().props.site || {});
const updated = new Date().toLocaleDateString('en-US', { month: 'long', year: 'numeric' });

const sections = computed(() => [
    ['1. Information we collect', 'We collect details you provide for visa, ticket and package services — including your name, contact details, passport information, date of birth, nationality, travel plans and uploaded documents (e.g. passport scans and photos). We also collect basic technical data such as your IP address and browser type.'],
    ['2. How we use your data', 'We use your information solely to process and manage your applications and bookings, to communicate with you about them, to submit applications to the relevant embassies/airlines on your behalf, and to meet legal obligations. We do not sell your personal data.'],
    ['3. Sharing your data', 'We share your data only as necessary to deliver our services — for example with embassies, consulates, visa centres, airlines and accommodation or insurance providers relevant to your request. These third parties handle your data under their own policies.'],
    ['4. Document security', 'Uploaded documents are stored privately and access is restricted to authorised staff. We retain them only as long as needed to provide our services and meet legal requirements, after which they are deleted.'],
    ['5. Data retention', 'We keep your information for as long as your account or application is active, and for a reasonable period afterwards for record-keeping and legal compliance.'],
    ['6. Your rights', 'You may request access to, correction of, or deletion of your personal data, subject to legal limits. To make a request, contact us using the details below.'],
    ['7. Cookies', 'Our website uses essential cookies needed for it to function (e.g. login sessions). We keep non-essential tracking to a minimum.'],
    ['8. Changes', 'We may update this policy from time to time. The latest version will always be available on this page.'],
    ['9. Contact us', `For privacy questions or requests, email ${site.value.email} or call ${site.value.phone}. Office: ${site.value.address}.`],
]);
</script>

<template>
    <Head :title="`Privacy Policy — ${site.company}`" />
    <PublicLayout>
        <section class="relative overflow-hidden brand-mesh">
            <div class="relative mx-auto max-w-4xl px-4 py-14 sm:px-6 lg:px-8 lg:py-16">
                <nav class="text-sm text-slate-500"><Link href="/" class="hover:text-brand-purple">Home</Link> <span class="mx-2">/</span> <span class="text-brand-ink">Privacy Policy</span></nav>
                <h1 class="mt-4 font-heading text-4xl font-extrabold text-brand-ink sm:text-5xl">Privacy <span class="text-gradient">Policy</span></h1>
                <p class="mt-3 text-slate-500">Last updated {{ updated }}</p>
            </div>
            <div class="gradient-rule"></div>
        </section>

        <section class="mx-auto max-w-3xl px-4 py-14 sm:px-6 lg:px-8">
            <div class="space-y-8 leading-relaxed text-slate-700">
                <p>{{ site.company }} (“we”, “us”) respects your privacy. This policy explains what personal data we collect when you use our website and services, how we use it, and your rights.</p>
                <div v-for="s in sections" :key="s[0]">
                    <h2 class="font-heading text-xl font-semibold text-brand-ink">{{ s[0] }}</h2>
                    <p class="mt-2">{{ s[1] }}</p>
                </div>
                <p class="text-sm text-slate-400">This is a general template. Please have it reviewed by a legal professional before relying on it.</p>
            </div>
        </section>
    </PublicLayout>
</template>
