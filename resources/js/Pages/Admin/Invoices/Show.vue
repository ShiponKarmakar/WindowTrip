<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({ invoice: Object, statuses: Array });
const inv = computed(() => props.invoice);

const money = (n) => Number(n || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
const badge = (s) => ({
    draft: 'bg-slate-100 text-slate-500', sent: 'bg-sky-50 text-sky-600',
    partial: 'bg-amber-50 text-amber-600', paid: 'bg-emerald-50 text-emerald-600',
    cancelled: 'bg-red-50 text-red-500',
}[s] || 'bg-slate-100 text-slate-500');

const pay = useForm({
    amount: props.invoice.balance > 0 ? props.invoice.balance : '',
    date: new Date().toISOString().slice(0, 10),
    method: 'Bank transfer',
    note: '',
});
function recordPayment() {
    pay.post(route('admin.invoices.payment', inv.value.id), { preserveScroll: true, onSuccess: () => pay.reset('note') });
}
function setStatus(status) {
    router.patch(route('admin.invoices.status', inv.value.id), { status }, { preserveScroll: true });
}
function emailInvoice() {
    if (confirm(`Email invoice ${inv.value.number} to ${inv.value.client_email}?`)) {
        router.post(route('admin.invoices.email', inv.value.id), {}, { preserveScroll: true });
    }
}
function remove() {
    if (confirm(`Delete invoice ${inv.value.number}? This cannot be undone.`)) {
        router.delete(route('admin.invoices.destroy', inv.value.id));
    }
}
</script>

<template>
    <Head :title="inv.number" />
    <AdminLayout>
        <template #title>Invoice {{ inv.number }}</template>

        <div class="flex flex-wrap items-center justify-between gap-3">
            <Link href="/admin/invoices" class="text-sm font-medium text-slate-500 hover:text-brand-purple">← Back to invoices</Link>
            <div class="flex flex-wrap gap-2">
                <a :href="route('admin.invoices.pdf', inv.id)" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-brand-ink hover:border-brand-purple/40">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0l-4-4m4 4l4-4M5 21h14"/></svg>
                    PDF
                </a>
                <button @click="emailInvoice" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-brand-ink hover:border-brand-purple/40">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l9 6 9-6M5 5h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2z"/></svg>
                    Email client
                </button>
                <Link :href="route('admin.invoices.edit', inv.id)" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-brand-ink hover:border-brand-purple/40">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 4H4v16h16v-7M18.5 2.5a2.1 2.1 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Edit
                </Link>
                <button @click="remove" class="inline-flex items-center gap-2 rounded-full border border-red-200 bg-white px-4 py-2 text-sm font-semibold text-red-500 hover:bg-red-50">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M9 7V5a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2m2 0v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7"/></svg>
                    Delete
                </button>
            </div>
        </div>

        <div class="mt-4 grid gap-6 lg:grid-cols-3">
            <!-- Invoice body -->
            <div class="space-y-6 lg:col-span-2">
                <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="font-mono text-sm text-brand-purple">{{ inv.number }}</div>
                            <div class="mt-1 text-sm text-slate-500">Issued {{ inv.issue_date }}<span v-if="inv.due_date"> · Due {{ inv.due_date }}</span></div>
                        </div>
                        <span class="rounded-full px-3 py-1 text-xs font-medium capitalize" :class="badge(inv.status)">{{ inv.status }}</span>
                    </div>
                    <div class="mt-4 border-t border-slate-100 pt-4">
                        <div class="text-xs uppercase tracking-wide text-slate-400">Bill to</div>
                        <div class="font-semibold text-brand-ink">{{ inv.client_name }}</div>
                        <div class="text-sm text-slate-500">{{ inv.client_email }}<span v-if="inv.client_phone"> · {{ inv.client_phone }}</span></div>
                    </div>

                    <table class="mt-5 w-full text-left text-sm">
                        <thead class="text-xs uppercase tracking-wide text-slate-400">
                            <tr><th class="py-2">Description</th><th class="py-2 text-right">Qty</th><th class="py-2 text-right">Unit</th><th class="py-2 text-right">Amount</th></tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="(it, i) in inv.items" :key="i">
                                <td class="py-2 text-brand-ink">{{ it.description }}</td>
                                <td class="py-2 text-right text-slate-600">{{ it.qty }}</td>
                                <td class="py-2 text-right text-slate-600">{{ money(it.unit_price) }}</td>
                                <td class="py-2 text-right font-medium text-brand-ink">{{ money((Number(it.qty)||0)*(Number(it.unit_price)||0)) }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="mt-4 flex flex-col items-end gap-1 text-sm">
                        <div class="flex w-56 justify-between"><span class="text-slate-500">Subtotal</span><span>{{ inv.currency }} {{ money(inv.subtotal) }}</span></div>
                        <div v-if="Number(inv.discount) > 0" class="flex w-56 justify-between"><span class="text-slate-500">Discount</span><span>− {{ money(inv.discount) }}</span></div>
                        <div v-if="Number(inv.tax) > 0" class="flex w-56 justify-between"><span class="text-slate-500">Tax</span><span>{{ money(inv.tax) }}</span></div>
                        <div class="flex w-56 justify-between border-t border-slate-100 pt-2 font-heading text-lg font-bold text-brand-ink"><span>Total</span><span>{{ inv.currency }} {{ money(inv.total) }}</span></div>
                        <div v-if="Number(inv.amount_paid) > 0" class="flex w-56 justify-between text-emerald-600"><span>Paid</span><span>− {{ money(inv.amount_paid) }}</span></div>
                        <div v-if="inv.balance > 0" class="flex w-56 justify-between font-semibold text-amber-600"><span>Balance due</span><span>{{ inv.currency }} {{ money(inv.balance) }}</span></div>
                    </div>

                    <div v-if="inv.notes" class="mt-5 border-t border-slate-100 pt-4 text-sm text-slate-600"><span class="text-xs uppercase tracking-wide text-slate-400">Notes</span><p class="mt-1">{{ inv.notes }}</p></div>
                </div>

                <!-- Payments log -->
                <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                    <h3 class="font-heading font-semibold text-brand-ink">Payments</h3>
                    <div v-if="inv.payments?.length" class="mt-3 divide-y divide-slate-100 text-sm">
                        <div v-for="(p, i) in inv.payments" :key="i" class="flex items-center justify-between py-2">
                            <div><span class="font-medium text-brand-ink">{{ inv.currency }} {{ money(p.amount) }}</span> <span class="text-slate-400">· {{ p.method }}</span><span v-if="p.note" class="text-slate-400"> · {{ p.note }}</span></div>
                            <span class="text-slate-500">{{ p.date }}</span>
                        </div>
                    </div>
                    <p v-else class="mt-2 text-sm text-slate-400">No payments recorded yet.</p>
                </div>
            </div>

            <!-- Sidebar actions -->
            <aside class="space-y-6">
                <div v-if="inv.status !== 'paid' && inv.status !== 'cancelled'" class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                    <h3 class="font-heading font-semibold text-brand-ink">Record payment</h3>
                    <div class="mt-3 space-y-3">
                        <div><label class="lbl">Amount ({{ inv.currency }})</label><input v-model.number="pay.amount" type="number" step="0.01" min="0.01" class="inp" /><p v-if="pay.errors.amount" class="err">{{ pay.errors.amount }}</p></div>
                        <div><label class="lbl">Date</label><input v-model="pay.date" type="date" class="inp" /></div>
                        <div><label class="lbl">Method</label><input v-model="pay.method" class="inp" placeholder="Bank transfer / bKash / Cash" /></div>
                        <div><label class="lbl">Note</label><input v-model="pay.note" class="inp" placeholder="Reference / txn id" /></div>
                        <button @click="recordPayment" :disabled="pay.processing" class="w-full rounded-full bg-brand-gradient px-5 py-2.5 text-sm font-semibold text-white shadow-brand hover:opacity-90 disabled:opacity-50">Record payment</button>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                    <h3 class="font-heading font-semibold text-brand-ink">Status</h3>
                    <div class="mt-3 flex flex-col gap-2">
                        <button v-if="inv.status === 'draft'" @click="setStatus('sent')" class="rounded-xl bg-sky-50 px-4 py-2.5 text-sm font-semibold text-sky-600 hover:bg-sky-100">Mark as sent</button>
                        <button v-if="inv.status !== 'cancelled' && inv.status !== 'paid'" @click="setStatus('cancelled')" class="rounded-xl bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-500 hover:bg-red-100">Cancel invoice</button>
                        <button v-if="inv.status === 'cancelled'" @click="setStatus('draft')" class="rounded-xl bg-slate-100 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-200">Reopen as draft</button>
                        <p v-if="inv.status === 'paid'" class="rounded-xl bg-emerald-50 px-4 py-2.5 text-sm font-medium text-emerald-700">✓ Fully paid</p>
                    </div>
                </div>
            </aside>
        </div>
    </AdminLayout>
</template>

<style scoped>
.lbl { @apply mb-1 block text-sm font-medium text-slate-700; }
.inp { @apply w-full rounded-xl border-slate-200 text-sm focus:border-brand-purple focus:ring-brand-purple; }
.err { @apply mt-1 text-xs text-red-500; }
</style>
