<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const page = usePage();
const navVisas = computed(() => page.props.navVisas || []);
const user = computed(() => page.props.auth?.user);
const mobileOpen = ref(false);
</script>

<template>
    <header class="sticky top-0 z-50 border-b border-slate-100 bg-white/90 backdrop-blur">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-20 items-center justify-between">
                <Link :href="route('home')" class="flex items-center">
                    <img :src="$page.props.site?.logo || '/brand/logo-horizontal.svg'" alt="Window Trip" class="h-10 w-auto" />
                </Link>

                <nav class="hidden items-center gap-8 text-sm font-medium text-slate-700 md:flex">
                    <Link :href="route('home')" class="transition hover:text-brand-purple">Home</Link>

                    <div class="group relative">
                        <Link :href="route('visa.index')" class="flex items-center gap-1 transition hover:text-brand-purple group-hover:text-brand-purple">
                            Visa Processing
                            <svg class="h-4 w-4 transition group-hover:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
                        </Link>
                        <div class="invisible absolute left-1/2 top-full z-50 w-[34rem] -translate-x-1/2 pt-4 opacity-0 transition duration-150 group-hover:visible group-hover:opacity-100">
                            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-brand">
                                <div class="grid grid-cols-2 gap-1">
                                    <Link v-for="v in navVisas" :key="v.slug" :href="route('visa.show', v.slug)" class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition hover:bg-brand-50">
                                        <span class="text-2xl">{{ v.flag }}</span>
                                        <span>
                                            <span class="block font-semibold text-brand-ink">{{ v.name }}</span>
                                            <span class="block text-xs text-slate-500">{{ v.processing }} · from ৳{{ v.fee_from }}</span>
                                        </span>
                                    </Link>
                                </div>
                                <Link :href="route('visa.index')" class="mt-3 flex items-center justify-between rounded-xl bg-brand-gradient px-4 py-3 text-sm font-semibold text-white">
                                    View all visa destinations <span>→</span>
                                </Link>
                            </div>
                        </div>
                    </div>

                    <Link :href="route('tickets.index')" class="transition hover:text-brand-purple">Air Tickets</Link>
                    <Link :href="route('packages.index')" class="transition hover:text-brand-purple">Tour Packages</Link>
                    <Link :href="route('track')" class="transition hover:text-brand-purple">Track</Link>
                    <Link :href="route('contact')" class="transition hover:text-brand-purple">Contact</Link>
                </nav>

                <div class="flex items-center gap-3">
                    <template v-if="user">
                        <Link :href="route('dashboard')" class="hidden rounded-full bg-brand-gradient px-5 py-2.5 text-sm font-semibold text-white shadow-brand transition hover:opacity-90 sm:inline">My Portal</Link>
                    </template>
                    <template v-else>
                        <Link :href="route('login')" class="hidden text-sm font-medium text-slate-700 transition hover:text-brand-purple sm:inline">Sign in</Link>
                        <Link :href="route('register')" class="hidden rounded-full bg-brand-gradient px-5 py-2.5 text-sm font-semibold text-white shadow-brand transition hover:opacity-90 sm:inline">Get Started</Link>
                    </template>

                    <button type="button" @click="mobileOpen = !mobileOpen" aria-label="Toggle menu" :aria-expanded="mobileOpen" class="inline-flex h-10 w-10 items-center justify-center rounded-xl text-brand-ink hover:bg-slate-100 md:hidden">
                        <svg v-if="!mobileOpen" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
                        <svg v-else class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div v-show="mobileOpen" class="border-t border-slate-100 bg-white md:hidden">
            <nav class="mx-auto max-w-7xl space-y-1 px-4 py-4 text-sm font-medium text-slate-700" @click="mobileOpen = false">
                <Link :href="route('home')" class="block rounded-xl px-3 py-2.5 hover:bg-brand-50">Home</Link>
                <Link :href="route('visa.index')" class="block rounded-xl px-3 py-2.5 hover:bg-brand-50">Visa Processing</Link>
                <div class="grid grid-cols-2 gap-1 px-2 pb-1">
                    <Link v-for="v in navVisas" :key="v.slug" :href="route('visa.show', v.slug)" class="flex items-center gap-2 rounded-lg px-2 py-2 text-xs hover:bg-brand-50">
                        <span class="text-lg">{{ v.flag }}</span> {{ v.name }}
                    </Link>
                </div>
                <Link :href="route('tickets.index')" class="block rounded-xl px-3 py-2.5 hover:bg-brand-50">Air Tickets</Link>
                <Link :href="route('packages.index')" class="block rounded-xl px-3 py-2.5 hover:bg-brand-50">Tour Packages</Link>
                <Link :href="route('track')" class="block rounded-xl px-3 py-2.5 hover:bg-brand-50">Track</Link>
                <Link :href="route('contact')" class="block rounded-xl px-3 py-2.5 hover:bg-brand-50">Contact</Link>
                <div class="mt-3 grid gap-2 border-t border-slate-100 pt-4">
                    <Link v-if="user" :href="route('dashboard')" class="rounded-full bg-brand-gradient px-5 py-3 text-center font-semibold text-white">My Portal</Link>
                    <template v-else>
                        <Link :href="route('login')" class="rounded-full px-5 py-3 text-center font-semibold text-brand-ink ring-1 ring-slate-200">Sign in</Link>
                        <Link :href="route('register')" class="rounded-full bg-brand-gradient px-5 py-3 text-center font-semibold text-white">Get Started</Link>
                    </template>
                </div>
            </nav>
        </div>
    </header>
</template>
