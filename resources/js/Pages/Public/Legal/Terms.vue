<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const site = computed(() => usePage().props.site || {});
const updated = new Date().toLocaleDateString('en-US', { month: 'long', year: 'numeric' });

const sections = computed(() => [
    ['1. Our services', `${site.value.company} provides tourist visa processing assistance, air ticket booking and tour package arrangements. We prepare, review and submit applications on your behalf and keep you informed of progress.`],
    ['2. No guarantee of outcome', 'We do not control and are not responsible for the decisions of any embassy, consulate or airline. Approval, refusal, additional document requests, interview outcomes and processing times are determined by those authorities. Service fees are non-refundable once work has begun, even if a visa is refused.'],
    ['3. Accuracy of information', 'You are responsible for providing true, complete and accurate information and documents. We are not liable for delays or refusals caused by incorrect, incomplete, misleading or fraudulent information supplied by you.'],
    ['4. Fees & payments', 'Service fees are quoted separately from government/embassy fees, airline fares and third-party charges, which are payable in addition. Fees and prices shown are indicative and may change without notice until confirmed.'],
    ['5. Documents', 'You must submit valid documents within requested timeframes. We handle your documents with care but are not liable for loss or damage caused by third parties such as embassies or courier services.'],
    ['6. Air tickets & packages', 'Tickets, bookings and packages are subject to the terms, availability, fare rules and cancellation policies of the airlines and suppliers involved.'],
    ['7. Limitation of liability', `To the maximum extent permitted by law, ${site.value.company}'s total liability for any claim is limited to the service fee you paid us for the affected service. We are not liable for indirect or consequential losses.`],
    ['8. Cancellations', 'Cancellation and refund terms depend on the stage of processing and any third-party charges already incurred. Contact us for details specific to your case.'],
    ['9. Changes to these terms', 'We may update these terms from time to time. Continued use of our services constitutes acceptance of the current terms.'],
    ['10. Contact', `Questions about these terms? Email ${site.value.email} or call ${site.value.phone}.`],
]);
</script>

<template>
    <Head :title="`Terms & Conditions — ${site.company}`" />
    <PublicLayout>
        <section class="relative overflow-hidden brand-mesh">
            <div class="relative mx-auto max-w-4xl px-4 py-14 sm:px-6 lg:px-8 lg:py-16">
                <nav class="text-sm text-slate-500"><Link href="/" class="hover:text-brand-purple">Home</Link> <span class="mx-2">/</span> <span class="text-brand-ink">Terms &amp; Conditions</span></nav>
                <h1 class="mt-4 font-heading text-4xl font-extrabold text-brand-ink sm:text-5xl">Terms &amp; <span class="text-gradient">Conditions</span></h1>
                <p class="mt-3 text-slate-500">Last updated {{ updated }}</p>
            </div>
            <div class="gradient-rule"></div>
        </section>

        <section class="mx-auto max-w-3xl px-4 py-14 sm:px-6 lg:px-8">
            <div class="space-y-8 leading-relaxed text-slate-700">
                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
                    <h2 class="font-heading text-lg font-bold text-amber-800">Important — Visa processing only</h2>
                    <p class="mt-2 text-amber-800">
                        {{ site.company }} is a travel agency that provides <strong>visa application processing and documentation
                        assistance only</strong>. We do <strong>not</strong> issue visas and <strong>cannot guarantee</strong> approval,
                        rejection, processing time or any specific outcome. Every visa decision rests <strong>solely with the relevant
                        embassy, consulate or immigration authority</strong>. Our service fees cover our processing work and are payable
                        regardless of the outcome.
                    </p>
                </div>

                <div v-for="s in sections" :key="s[0]">
                    <h2 class="font-heading text-xl font-semibold text-brand-ink">{{ s[0] }}</h2>
                    <p class="mt-2">{{ s[1] }}</p>
                </div>

                <p class="text-sm text-slate-400">This is a general template. Please have it reviewed by a legal professional before relying on it.</p>
            </div>
        </section>
    </PublicLayout>
</template>
