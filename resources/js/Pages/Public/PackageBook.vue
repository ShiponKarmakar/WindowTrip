<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({ package: { type: Object, required: true } });

const form = useForm({ name: '', email: '', phone: '', travellers: 2, travel_date: '', message: '' });

function submit() {
    form.post(route('packages.book.store', props.package.slug), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}
</script>

<template>
    <Head :title="`Book ${package.title} — Window Trip`" />
    <PublicLayout>
        <section class="relative overflow-hidden brand-mesh">
            <div class="relative mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8 lg:py-14">
                <nav class="text-sm text-slate-500">
                    <Link href="/" class="hover:text-brand-purple">Home</Link> <span class="mx-2">/</span>
                    <Link :href="route('packages.index')" class="hover:text-brand-purple">Tour Packages</Link> <span class="mx-2">/</span>
                    <span class="text-brand-ink">{{ package.title }}</span>
                </nav>
                <h1 class="mt-4 font-heading text-3xl font-extrabold text-brand-ink sm:text-4xl">Book <span class="text-gradient">{{ package.title }}</span></h1>
            </div>
            <div class="gradient-rule"></div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid gap-8 lg:grid-cols-3">
                <div class="lg:col-span-1">
                    <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm lg:sticky lg:top-28">
                        <div class="relative h-36" :style="{ backgroundColor: package.color || '#139dd5' }">
                            <div class="absolute inset-0 dot-grid opacity-30"></div>
                            <span v-if="package.tag" class="absolute left-4 top-4 rounded-full bg-white/90 px-3 py-1 text-xs font-semibold text-brand-ink">{{ package.tag }}</span>
                            <span class="absolute bottom-3 right-4 text-4xl text-white/40">✈︎</span>
                        </div>
                        <div class="p-6">
                            <h2 class="font-heading text-lg font-semibold text-brand-ink">{{ package.title }}</h2>
                            <p class="text-sm text-slate-500">{{ package.destination }} · {{ package.nights }}</p>
                            <div class="mt-3">
                                <span class="text-xs text-slate-500">From</span>
                                <div class="font-heading text-2xl font-bold text-brand-ink">৳{{ package.price }}</div>
                                <span class="text-xs text-slate-500">per person (indicative)</span>
                            </div>
                            <ul v-if="package.includes?.length" class="mt-4 space-y-1.5 border-t border-slate-100 pt-4 text-sm text-slate-600">
                                <li v-for="inc in package.includes" :key="inc" class="flex items-center gap-2"><span class="text-emerald-500">✓</span> {{ inc }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm sm:p-8">
                        <h2 class="font-heading text-xl font-bold text-brand-ink">Request your booking</h2>
                        <p class="mt-1 text-sm text-slate-500">Tell us your details — we’ll confirm availability and final pricing with you.</p>
                        <form @submit.prevent="submit" class="mt-6 space-y-5">
                            <div class="grid gap-5 sm:grid-cols-2">
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Your name</label>
                                    <input v-model="form.name" class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple" />
                                    <p v-if="form.errors.name" class="mt-1 text-xs text-red-500">{{ form.errors.name }}</p>
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Email</label>
                                    <input v-model="form.email" type="email" class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple" />
                                    <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">{{ form.errors.email }}</p>
                                </div>
                            </div>
                            <div class="grid gap-5 sm:grid-cols-3">
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Phone</label>
                                    <input v-model="form.phone" placeholder="+880…" class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple" />
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Travellers</label>
                                    <input v-model.number="form.travellers" type="number" min="1" max="30" class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple" />
                                    <p v-if="form.errors.travellers" class="mt-1 text-xs text-red-500">{{ form.errors.travellers }}</p>
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Preferred date</label>
                                    <input v-model="form.travel_date" type="date" class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple" />
                                    <p v-if="form.errors.travel_date" class="mt-1 text-xs text-red-500">{{ form.errors.travel_date }}</p>
                                </div>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-slate-700">Special requests <span class="text-slate-500">(optional)</span></label>
                                <textarea v-model="form.message" rows="3" placeholder="Room preference, add-ons, anything else…" class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple"></textarea>
                            </div>
                            <button type="submit" :disabled="form.processing" class="rounded-full bg-brand-gradient px-7 py-3.5 font-semibold text-white shadow-brand transition hover:opacity-90 disabled:opacity-50">Request Booking</button>
                            <p class="text-xs text-slate-500">No payment now — this sends a booking request. Our team confirms availability and final price before anything is charged.</p>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
