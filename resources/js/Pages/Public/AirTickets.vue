<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    trip_type: 'round_trip', from: '', to: '', depart_date: '', return_date: '',
    passengers: 1, cabin: 'Economy', name: '', email: '', phone: '', notes: '',
});

function submit() {
    form.post(route('tickets.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}

const benefits = [
    ['🎫', 'Best available fares', 'We compare airlines and unpublished deals for you.'],
    ['⚡', 'Fast turnaround', 'Get fare options the same day, often within hours.'],
    ['🌍', 'Domestic & international', 'Any route, any airline — economy to business.'],
    ['🤝', 'Human support', 'A real agent handles your booking end to end.'],
];
</script>

<template>
    <Head title="Air Tickets — Request Best Fares | Window Trip" />
    <PublicLayout>
        <section class="relative overflow-hidden brand-mesh">
            <div class="pointer-events-none absolute -top-24 right-0 h-72 w-72 animate-blob rounded-full bg-brand-magenta/20 blur-3xl"></div>
            <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
                <nav class="text-sm text-slate-500"><Link href="/" class="hover:text-brand-purple">Home</Link> <span class="mx-2">/</span> <span class="text-brand-ink">Air Tickets</span></nav>
                <h1 class="mt-4 font-heading text-4xl font-extrabold text-brand-ink sm:text-5xl">Air <span class="text-gradient">Tickets</span></h1>
                <p class="mt-4 max-w-2xl text-lg text-slate-600">No endless searching. Just tell us where and when — we source the best fares and issue your ticket.</p>
            </div>
            <div class="gradient-rule"></div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <div class="grid gap-10 lg:grid-cols-3">
                <div class="space-y-4">
                    <div v-for="b in benefits" :key="b[1]" class="flex items-start gap-4 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                        <span class="text-2xl">{{ b[0] }}</span>
                        <div>
                            <div class="font-heading font-semibold text-brand-ink">{{ b[1] }}</div>
                            <p class="mt-0.5 text-sm text-slate-600">{{ b[2] }}</p>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm sm:p-8">
                        <h2 class="font-heading text-xl font-bold text-brand-ink">Request a ticket</h2>
                        <p class="mt-1 text-sm text-slate-500">Fill in your trip and we’ll send you fare options.</p>
                        <form @submit.prevent="submit" class="mt-6 space-y-5">
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 text-sm font-medium text-slate-700"><input type="radio" value="round_trip" v-model="form.trip_type" class="text-brand-purple focus:ring-brand-purple"> Round trip</label>
                                <label class="flex items-center gap-2 text-sm font-medium text-slate-700"><input type="radio" value="one_way" v-model="form.trip_type" class="text-brand-purple focus:ring-brand-purple"> One way</label>
                            </div>
                            <div class="grid gap-5 sm:grid-cols-2">
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-slate-700">From</label>
                                    <input v-model="form.from" placeholder="Dhaka (DAC)" class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple" />
                                    <p v-if="form.errors.from" class="mt-1 text-xs text-red-500">{{ form.errors.from }}</p>
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-slate-700">To</label>
                                    <input v-model="form.to" placeholder="Bangkok (BKK)" class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple" />
                                    <p v-if="form.errors.to" class="mt-1 text-xs text-red-500">{{ form.errors.to }}</p>
                                </div>
                            </div>
                            <div class="grid gap-5 sm:grid-cols-3">
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Departure</label>
                                    <input v-model="form.depart_date" type="date" class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple" />
                                    <p v-if="form.errors.depart_date" class="mt-1 text-xs text-red-500">{{ form.errors.depart_date }}</p>
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Return <span class="text-slate-500">(optional)</span></label>
                                    <input v-model="form.return_date" type="date" class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple" />
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Passengers</label>
                                    <input v-model.number="form.passengers" type="number" min="1" max="20" class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple" />
                                </div>
                            </div>
                            <div class="grid gap-5 sm:grid-cols-2">
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Cabin class</label>
                                    <select v-model="form.cabin" class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple">
                                        <option v-for="c in ['Economy','Premium Economy','Business','First']" :key="c" :value="c">{{ c }}</option>
                                    </select>
                                </div>
                            </div>
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
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-slate-700">Phone <span class="text-slate-500">(optional)</span></label>
                                <input v-model="form.phone" class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple" />
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-slate-700">Notes <span class="text-slate-500">(optional)</span></label>
                                <textarea v-model="form.notes" rows="2" class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple"></textarea>
                            </div>
                            <button type="submit" :disabled="form.processing" class="rounded-full bg-brand-gradient px-7 py-3.5 font-semibold text-white shadow-brand transition hover:opacity-90 disabled:opacity-50">Request fares</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
