<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({ ticket: Object, statuses: Array });
const t = computed(() => props.ticket);
const showSeat = computed(() => (t.value.passengers || []).some((p) => p.seat));

const badge = (s) => ({
    draft: 'bg-slate-100 text-slate-500', issued: 'bg-emerald-50 text-emerald-600',
    cancelled: 'bg-red-50 text-red-500',
}[s] || 'bg-slate-100 text-slate-500');

const money = (n) => Number(n || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
const fmt = (v) => {
    if (!v) return '—';
    const d = new Date(v);
    return isNaN(d) ? v : d.toLocaleString(undefined, { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
};

function setStatus(status) {
    router.patch(route('admin.tickets.status', t.value.id), { status }, { preserveScroll: true });
}
function emailTicket() {
    if (confirm(`Email e-ticket ${t.value.number} to ${t.value.client_email}?`)) {
        router.post(route('admin.tickets.email', t.value.id), {}, { preserveScroll: true });
    }
}
function remove() {
    if (confirm(`Delete ticket ${t.value.number}? This cannot be undone.`)) {
        router.delete(route('admin.tickets.destroy', t.value.id));
    }
}
</script>

<template>
    <Head :title="t.number" />
    <AdminLayout>
        <template #title>Ticket {{ t.number }}</template>

        <div class="flex flex-wrap items-center justify-between gap-3">
            <Link href="/admin/tickets" class="text-sm font-medium text-slate-500 hover:text-brand-purple">← Back to tickets</Link>
            <div class="flex flex-wrap gap-2">
                <a :href="route('admin.tickets.pdf', t.id)" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-brand-ink hover:border-brand-purple/40">⬇ WindowTrip PDF</a>
                <a :href="route('admin.tickets.pdf', { flightTicket: t.id, print: 1 })" target="_blank" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-brand-ink hover:border-brand-purple/40">🖨 Print</a>
                <a v-if="t.has_source" :href="route('admin.tickets.source', t.id)" target="_blank" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-brand-ink hover:border-brand-purple/40">📎 Original</a>
                <button @click="emailTicket" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-brand-ink hover:border-brand-purple/40">✉ Email client</button>
                <Link :href="route('admin.tickets.edit', t.id)" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-brand-ink hover:border-brand-purple/40">✏️ Edit</Link>
                <button @click="remove" class="rounded-full border border-red-200 bg-white px-4 py-2 text-sm font-semibold text-red-500 hover:bg-red-50">Delete</button>
            </div>
        </div>

        <div class="mt-4 grid gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <!-- Header -->
                <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="font-mono text-sm text-brand-purple">{{ t.number }}</div>
                            <div class="mt-1 text-sm text-slate-500">Issued {{ t.issue_date }}<span v-if="t.airline"> · {{ t.airline }}</span></div>
                        </div>
                        <span class="rounded-full px-3 py-1 text-xs font-medium capitalize" :class="badge(t.status)">{{ t.status }}</span>
                    </div>
                    <div class="mt-4 grid gap-4 border-t border-slate-100 pt-4 sm:grid-cols-2">
                        <div>
                            <div class="text-xs uppercase tracking-wide text-slate-400">Passenger contact</div>
                            <div class="font-semibold text-brand-ink">{{ t.client_name }}</div>
                            <div class="text-sm text-slate-500">{{ t.client_email }}<span v-if="t.client_phone"> · {{ t.client_phone }}</span></div>
                        </div>
                        <div>
                            <div class="text-xs uppercase tracking-wide text-slate-400">Booking PNR</div>
                            <div class="font-mono text-lg font-bold tracking-widest text-brand-ink">{{ t.pnr }}</div>
                            <div v-if="t.booking_ref" class="text-xs text-slate-500">Airline ref: {{ t.booking_ref }}</div>
                        </div>
                    </div>
                </div>

                <!-- Itinerary -->
                <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                    <h3 class="font-heading font-semibold text-brand-ink">Itinerary</h3>
                    <div class="mt-4 space-y-3">
                        <div v-for="(s, i) in t.segments" :key="i" class="rounded-xl border border-slate-100 p-4">
                            <div class="mb-3 flex items-center justify-between text-xs">
                                <span class="font-semibold text-brand-ink">{{ s.airline || t.airline }} · {{ (s.flight_number || '').toUpperCase() }}</span>
                                <span class="text-slate-400">{{ s.cabin }}<span v-if="s.baggage"> · Check-in {{ s.baggage }}</span><span v-if="s.cabin_baggage"> · Cabin {{ s.cabin_baggage }}</span></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-heading text-xl font-bold text-brand-ink">{{ (s.from_code || '').toUpperCase() }}</div>
                                    <div class="text-xs text-slate-500">{{ s.from_city }}</div>
                                    <div class="mt-1 text-sm">{{ fmt(s.depart_at) }}</div>
                                </div>
                                <div class="px-3 text-brand-purple">✈</div>
                                <div class="text-right">
                                    <div class="font-heading text-xl font-bold text-brand-ink">{{ (s.to_code || '').toUpperCase() }}</div>
                                    <div class="text-xs text-slate-500">{{ s.to_city }}</div>
                                    <div class="mt-1 text-sm">{{ fmt(s.arrive_at) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Passengers -->
                <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                    <h3 class="font-heading font-semibold text-brand-ink">Passengers</h3>
                    <table class="mt-3 w-full text-left text-sm">
                        <thead class="text-xs uppercase tracking-wide text-slate-400">
                            <tr><th class="py-2">Name</th><th class="py-2">Type</th><th class="py-2">Ticket #</th><th v-if="showSeat" class="py-2">Seat</th></tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="(p, i) in t.passengers" :key="i">
                                <td class="py-2 font-medium text-brand-ink">{{ p.name }}</td>
                                <td class="py-2 capitalize text-slate-600">{{ p.type || 'adult' }}</td>
                                <td class="py-2 text-slate-600">{{ p.ticket_number || '—' }}</td>
                                <td v-if="showSeat" class="py-2 text-slate-600">{{ p.seat || '—' }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <div v-if="t.fare_total != null" class="mt-4 border-t border-slate-100 pt-3 text-right text-sm">
                        <span class="text-slate-500">Total fare: </span><span class="font-bold text-brand-ink">{{ t.currency }} {{ money(t.fare_total) }}</span>
                    </div>
                    <div v-if="t.notes" class="mt-4 border-t border-slate-100 pt-3 text-sm text-slate-600"><span class="text-xs uppercase tracking-wide text-slate-400">Notes</span><p class="mt-1">{{ t.notes }}</p></div>
                </div>
            </div>

            <!-- Sidebar -->
            <aside class="space-y-6">
                <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                    <h3 class="font-heading font-semibold text-brand-ink">Status</h3>
                    <div class="mt-3 flex flex-col gap-2">
                        <button v-if="t.status === 'draft'" @click="setStatus('issued')" class="rounded-xl bg-emerald-50 px-4 py-2.5 text-sm font-semibold text-emerald-600 hover:bg-emerald-100">Mark as issued</button>
                        <button v-if="t.status !== 'cancelled'" @click="setStatus('cancelled')" class="rounded-xl bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-500 hover:bg-red-100">Cancel ticket</button>
                        <button v-if="t.status === 'cancelled'" @click="setStatus('draft')" class="rounded-xl bg-slate-100 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-200">Reopen as draft</button>
                    </div>
                    <p class="mt-3 text-xs text-slate-400">Only issued (non-draft) tickets are visible to the customer in their portal.</p>
                </div>

                <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                    <h3 class="font-heading font-semibold text-brand-ink">Route</h3>
                    <p class="mt-2 text-sm font-medium text-brand-ink">{{ t.route || '—' }}</p>
                </div>
            </aside>
        </div>
    </AdminLayout>
</template>
