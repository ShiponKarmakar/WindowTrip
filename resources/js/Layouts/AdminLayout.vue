<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { computed, ref, watch, onMounted, onUnmounted } from 'vue';
import { debounce } from 'lodash';

const page = usePage();
const user = computed(() => page.props.auth.admin);
const roleLabel = computed(() => {
    const r = page.props.auth.adminRole;
    if (!r) return 'Staff';
    return { admin: 'Administrator', agent: 'Agent' }[r] || (r.charAt(0).toUpperCase() + r.slice(1));
});
const flash = computed(() => page.props.flash?.success);
const showFlash = ref(true);
const menuOpen = ref(false);

// Global search
const search = ref('');
const results = ref([]);
const searchOpen = ref(false);
const searching = ref(false);
const searchInput = ref(null);

// ⌘K / Ctrl+K focuses the search
function onKeydown(e) {
    if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
        e.preventDefault();
        searchInput.value?.focus();
    }
}
onMounted(() => document.addEventListener('keydown', onKeydown));
onUnmounted(() => document.removeEventListener('keydown', onKeydown));

const runSearch = debounce(async (q) => {
    if (!q || q.length < 2) { results.value = []; searchOpen.value = false; return; }
    searching.value = true;
    searchOpen.value = true;
    try {
        const { data } = await window.axios.get(route('admin.search'), { params: { q } });
        results.value = data.groups || [];
    } catch (e) {
        results.value = [];
    } finally {
        searching.value = false;
    }
}, 250);

watch(search, (v) => runSearch(v));

function closeSearch() { searchOpen.value = false; }
function gotoResult() { searchOpen.value = false; search.value = ''; results.value = []; }

// Local directive: close on outside click.
const vClickOutside = {
    mounted(el, binding) {
        el.__handler = (e) => { if (!el.contains(e.target)) binding.value(e); };
        document.addEventListener('click', el.__handler, true);
    },
    unmounted(el) {
        document.removeEventListener('click', el.__handler, true);
    },
};

const nav = [
    { label: 'Dashboard', href: '/admin', icon: 'M3 12l9-9 9 9M5 10v10h14V10' },
    { label: 'Visa Applications', href: '/admin/applications', icon: 'M9 12h6m-6 4h6M5 4h14v16H5z' },
    { label: 'Leads & Inquiries', href: '/admin/leads', icon: 'M3 8l9 6 9-6M5 5h14v14H5z' },
    { label: 'Clients', href: '/admin/clients', icon: 'M17 20h5v-1a4 4 0 0 0-4-4h-1m-6 5H2v-1a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v1zm-2-11a3 3 0 1 1-6 0 3 3 0 0 1 6 0zm7 1a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z' },
    { label: 'Invoices', href: '/admin/invoices', icon: 'M9 12h6m-6 4h6M9 8h6M6 3h12a1 1 0 0 1 1 1v17l-3-2-2 2-2-2-2 2-2-2-3 2V4a1 1 0 0 1 1-1z' },
    { label: 'Flight Tickets', href: '/admin/tickets', icon: 'M21 16v-2l-8-5V3.5a1.5 1.5 0 0 0-3 0V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L11 19v-5.5L21 16z' },
    { label: 'Visa Destinations', href: '/admin/visas', icon: 'M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8zm0 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6z' },
    { label: 'Tour Packages', href: '/admin/packages', icon: 'M3 7l9-4 9 4-9 4-9-4zm0 5l9 4 9-4M3 17l9 4 9-4' },
];

const isActive = (href) =>
    href === '/admin'
        ? page.url === '/admin'
        : page.url.startsWith(href);

function logout() {
    router.post(route('admin.logout'));
}

function closeMenu() {
    menuOpen.value = false;
}
</script>

<template>
    <div class="min-h-screen bg-slate-50">
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 hidden w-64 flex-col border-r border-slate-200 bg-white lg:flex">
            <div class="flex h-16 items-center border-b border-slate-100 px-6">
                <a href="/" class="flex items-center">
                    <img src="/brand/logo-horizontal.svg" alt="Window Trip" class="h-8 w-auto" />
                </a>
            </div>
            <nav class="flex-1 space-y-1 px-3 py-5">
                <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-wider text-slate-400">Manage</p>
                <Link
                    v-for="item in nav"
                    :key="item.href"
                    :href="item.href"
                    class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition"
                    :class="isActive(item.href) ? 'bg-brand-gradient text-white shadow-brand' : 'text-slate-600 hover:bg-slate-100'"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" :d="item.icon" /></svg>
                    {{ item.label }}
                </Link>
            </nav>
        </aside>

        <!-- Main -->
        <div class="lg:pl-64">
            <header class="sticky top-0 z-30 flex h-16 items-center gap-4 border-b border-slate-200 bg-white/90 px-5 backdrop-blur">
                <h1 class="hidden shrink-0 font-heading text-lg font-semibold text-brand-ink md:block">
                    <slot name="title">Admin</slot>
                </h1>

                <!-- Global search -->
                <div class="relative w-full max-w-sm" v-click-outside="closeSearch">
                    <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="M21 21l-4.3-4.3"/></svg>
                    <input
                        ref="searchInput"
                        v-model="search"
                        @focus="search.length >= 2 && (searchOpen = true)"
                        type="text"
                        placeholder="Search tickets, invoices, clients…"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-14 text-sm text-brand-ink placeholder:text-slate-400 focus:border-brand-purple focus:bg-white focus:ring-1 focus:ring-brand-purple"
                    />
                    <kbd class="pointer-events-none absolute right-3 top-1/2 hidden -translate-y-1/2 items-center rounded-md border border-slate-200 bg-white px-1.5 py-0.5 font-mono text-[11px] font-medium text-slate-400 sm:inline-flex">⌘K</kbd>
                    <div v-if="searchOpen" class="absolute left-0 right-0 z-40 mt-2 max-h-[70vh] overflow-auto rounded-2xl border border-slate-100 bg-white py-2 shadow-brand">
                        <div v-if="searching" class="px-4 py-3 text-sm text-slate-400">Searching…</div>
                        <template v-else-if="results.length">
                            <div v-for="g in results" :key="g.label">
                                <p class="px-4 pb-1 pt-2 text-xs font-semibold uppercase tracking-wide text-slate-400">{{ g.label }}</p>
                                <Link v-for="(it, idx) in g.items" :key="idx" :href="it.url" @click="gotoResult" class="block px-4 py-2 hover:bg-slate-50">
                                    <div class="text-sm font-medium text-brand-ink">{{ it.title }}</div>
                                    <div class="truncate text-xs text-slate-400">{{ it.sub }}</div>
                                </Link>
                            </div>
                        </template>
                        <div v-else class="px-4 py-3 text-sm text-slate-400">No matches for “{{ search }}”.</div>
                    </div>
                </div>

                <div class="ml-auto flex shrink-0 items-center gap-2.5">
                    <!-- Notifications -->
                    <button type="button" title="Notifications" class="rounded-xl border border-slate-200 bg-white p-2.5 text-slate-500 transition hover:bg-slate-50 hover:text-brand-purple">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 0 0-4-5.7V5a2 2 0 1 0-4 0v.3A6 6 0 0 0 6 11v3.2a2 2 0 0 1-.6 1.4L4 17h5m6 0a3 3 0 1 1-6 0"/></svg>
                    </button>

                    <!-- Profile dropdown -->
                    <div class="relative" v-click-outside="closeMenu">
                        <button @click="menuOpen = !menuOpen" class="flex items-center gap-2.5 rounded-xl border border-slate-200 bg-white py-1.5 pl-1.5 pr-3 transition hover:bg-slate-50">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-gradient text-sm font-semibold text-white">{{ user?.name?.charAt(0) }}</span>
                            <span class="hidden text-left leading-tight sm:block">
                                <span class="block text-sm font-semibold text-brand-ink">{{ user?.name }}</span>
                                <span class="block text-xs text-slate-400">{{ roleLabel }}</span>
                            </span>
                            <svg class="h-4 w-4 text-slate-400 transition" :class="menuOpen && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <transition
                            enter-active-class="transition duration-150 ease-out" enter-from-class="opacity-0 translate-y-1" enter-to-class="opacity-100 translate-y-0"
                            leave-active-class="transition duration-100 ease-in" leave-from-class="opacity-100" leave-to-class="opacity-0">
                            <div v-if="menuOpen" class="absolute right-0 mt-2 w-60 overflow-hidden rounded-2xl border border-slate-100 bg-white py-2 shadow-brand">
                                <div class="border-b border-slate-100 px-4 pb-3 pt-1">
                                    <div class="text-sm font-semibold text-brand-ink">{{ user?.name }}</div>
                                    <div class="truncate text-xs text-slate-400">{{ user?.email }}</div>
                                    <span class="mt-1 inline-block rounded-full bg-brand-50 px-2 py-0.5 text-[10px] font-medium text-brand-600">Staff</span>
                                </div>
                                <Link href="/admin/profile" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50" @click="menuOpen = false">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 4-6 8-6s8 2 8 6"/></svg>
                                    My Profile
                                </Link>
                                <Link href="/admin/settings" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50" @click="menuOpen = false">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M12 3v2m0 14v2m9-9h-2M5 12H3m14.7-6.7l-1.4 1.4M7.7 16.3l-1.4 1.4m0-12.4l1.4 1.4m9 9l1.4 1.4"/></svg>
                                    Settings
                                </Link>
                                <a href="/" target="_blank" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5h5v5M19 5l-9 9M10 5H5v14h14v-5"/></svg>
                                    View website
                                </a>
                                <button @click="logout" class="flex w-full items-center gap-2.5 border-t border-slate-100 px-4 py-2.5 text-sm text-red-500 hover:bg-red-50">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 17l5-5-5-5M21 12H9M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/></svg>
                                    Sign out
                                </button>
                            </div>
                        </transition>
                    </div>
                </div>
            </header>

            <!-- Flash -->
            <div v-if="flash && showFlash" class="mx-5 mt-4 flex items-center justify-between rounded-xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                {{ flash }}
                <button @click="showFlash = false" class="text-emerald-500 hover:text-emerald-700">✕</button>
            </div>

            <main class="p-5 sm:p-7">
                <slot />
            </main>
        </div>
    </div>
</template>
