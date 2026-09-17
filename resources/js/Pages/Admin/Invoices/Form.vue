<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ClientPicker from '@/Components/ClientPicker.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    invoice: { type: Object, default: null },
    prefill: { type: Object, default: null },
    nextNumber: { type: String, default: '' },
    clients: { type: Array, default: () => [] },
});

const isEdit = !!props.invoice;
const inv = props.invoice || {};
const pf = props.prefill || {};
const today = new Date().toISOString().slice(0, 10);

const form = useForm({
    number: inv.number || props.nextNumber || '',
    visa_application_id: inv.visa_application_id ?? pf.visa_application_id ?? null,
    user_id: inv.user_id ?? pf.user_id ?? null,
    client_name: inv.client_name || pf.client_name || '',
    client_email: inv.client_email || pf.client_email || '',
    client_phone: inv.client_phone || pf.client_phone || '',
    currency: inv.currency || 'BDT',
    issue_date: (inv.issue_date ? inv.issue_date.slice(0, 10) : today),
    due_date: inv.due_date ? inv.due_date.slice(0, 10) : '',
    discount: Number(inv.discount || 0),
    tax: Number(inv.tax || 0),
    status: inv.status || 'draft',
    notes: inv.notes || '',
    voucher: null,
    items: inv.items?.length
        ? inv.items.map((i) => ({ description: i.description, qty: Number(i.qty), unit_price: Number(i.unit_price) }))
        : [{ description: pf.item || '', qty: 1, unit_price: 0 }],
});

const subtotal = computed(() => form.items.reduce((s, i) => s + (Number(i.qty) || 0) * (Number(i.unit_price) || 0), 0));
const total = computed(() => subtotal.value - (Number(form.discount) || 0) + (Number(form.tax) || 0));
const money = (n) => Number(n).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });

function onClientSelect(client) {
    if (!client) return; // "walk-in" — keep whatever is typed
    form.client_name = client.name || '';
    form.client_email = client.email || '';
    form.client_phone = client.phone || '';
}
function addItem() { form.items.push({ description: '', qty: 1, unit_price: 0 }); }
function onVoucher(e) { form.voucher = e.target.files[0] || null; }
function submit() {
    const opts = { preserveScroll: true, forceFormData: true };
    if (isEdit) {
        form.transform((d) => ({ ...d, _method: 'put' })).post(route('admin.invoices.update', props.invoice.id), opts);
    } else {
        form.post(route('admin.invoices.store'), opts);
    }
}
</script>

<template>
    <Head :title="isEdit ? `Edit ${inv.number}` : 'New Invoice'" />
    <AdminLayout>
        <template #title>{{ isEdit ? `Edit ${inv.number}` : 'New Invoice' }}</template>

        <Link href="/admin/invoices" class="text-sm font-medium text-slate-500 hover:text-brand-purple">← Back to invoices</Link>

        <form @submit.prevent="submit" class="mt-4 max-w-4xl space-y-6">
            <!-- Client + meta -->
            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <h3 class="font-heading font-semibold text-brand-ink">Client & details</h3>
                <div class="mt-4">
                    <ClientPicker v-model="form.user_id" :clients="clients" @select="onClientSelect" />
                </div>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div><label class="lbl">Client name</label><input v-model="form.client_name" class="inp" /><p v-if="form.errors.client_name" class="err">{{ form.errors.client_name }}</p></div>
                    <div><label class="lbl">Client email</label><input v-model="form.client_email" type="email" class="inp" /><p v-if="form.errors.client_email" class="err">{{ form.errors.client_email }}</p></div>
                    <div><label class="lbl">Client phone</label><input v-model="form.client_phone" class="inp" /></div>
                    <div><label class="lbl">Invoice number</label><input v-model="form.number" class="inp" /><p v-if="form.errors.number" class="err">{{ form.errors.number }}</p></div>
                    <div><label class="lbl">Issue date</label><input v-model="form.issue_date" type="date" class="inp" /><p v-if="form.errors.issue_date" class="err">{{ form.errors.issue_date }}</p></div>
                    <div><label class="lbl">Due date</label><input v-model="form.due_date" type="date" class="inp" /><p v-if="form.errors.due_date" class="err">{{ form.errors.due_date }}</p></div>
                    <div><label class="lbl">Currency</label><input v-model="form.currency" class="inp" /></div>
                    <div>
                        <label class="lbl">Status</label>
                        <select v-model="form.status" class="inp capitalize"><option value="draft">draft</option><option value="sent">sent</option><option value="cancelled">cancelled</option></select>
                    </div>
                </div>
            </div>

            <!-- Line items -->
            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h3 class="font-heading font-semibold text-brand-ink">Line items</h3>
                    <button type="button" @click="addItem" class="text-sm font-semibold text-brand-purple">+ Add item</button>
                </div>
                <p v-if="form.errors.items" class="err">{{ form.errors.items }}</p>
                <div class="mt-4 space-y-2">
                    <div class="hidden grid-cols-12 gap-2 px-1 text-xs uppercase tracking-wide text-slate-400 sm:grid">
                        <div class="col-span-6">Description</div><div class="col-span-2 text-right">Qty</div><div class="col-span-2 text-right">Unit price</div><div class="col-span-2 text-right">Amount</div>
                    </div>
                    <div v-for="(it, i) in form.items" :key="i" class="grid grid-cols-12 items-center gap-2">
                        <input v-model="it.description" placeholder="Description" class="inp col-span-12 sm:col-span-6" />
                        <input v-model.number="it.qty" type="number" min="0" step="1" class="inp col-span-4 text-right sm:col-span-2" />
                        <input v-model.number="it.unit_price" type="number" min="0" step="0.01" class="inp col-span-5 text-right sm:col-span-2" />
                        <div class="col-span-2 text-right text-sm font-medium text-brand-ink sm:col-span-1">{{ money((Number(it.qty)||0)*(Number(it.unit_price)||0)) }}</div>
                        <button type="button" @click="form.items.splice(i,1)" class="col-span-1 text-slate-400 hover:text-red-500">✕</button>
                    </div>
                </div>

                <div class="mt-5 flex flex-col items-end gap-1 text-sm">
                    <div class="flex w-64 justify-between"><span class="text-slate-500">Subtotal</span><span>{{ form.currency }} {{ money(subtotal) }}</span></div>
                    <div class="flex w-64 items-center justify-between"><span class="text-slate-500">Discount</span><input v-model.number="form.discount" type="number" min="0" step="0.01" class="inp w-28 text-right" /></div>
                    <div class="flex w-64 items-center justify-between"><span class="text-slate-500">Tax</span><input v-model.number="form.tax" type="number" min="0" step="0.01" class="inp w-28 text-right" /></div>
                    <div class="mt-1 flex w-64 justify-between border-t border-slate-100 pt-2 font-heading text-lg font-bold text-brand-ink"><span>Total</span><span>{{ form.currency }} {{ money(total) }}</span></div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <label class="lbl">Notes <span class="text-slate-500">(optional)</span></label>
                <textarea v-model="form.notes" rows="3" placeholder="Payment instructions, bank details, thank-you note…" class="inp"></textarea>
            </div>

            <!-- Voucher upload -->
            <div class="rounded-2xl border border-dashed border-brand-purple/30 bg-brand-50/40 p-6">
                <h3 class="font-heading font-semibold text-brand-ink">Voucher <span class="text-slate-500">(optional)</span></h3>
                <p class="mt-1 text-sm text-slate-500">Attach a payment voucher, receipt or supporting document (PDF or image). Stored privately with this invoice.</p>
                <input type="file" accept=".pdf,.png,.jpg,.jpeg" @change="onVoucher" class="mt-3 block w-full text-sm text-slate-600 file:mr-3 file:rounded-full file:border-0 file:bg-brand-gradient file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white" />
                <p v-if="isEdit && inv.voucher_name" class="mt-2 text-xs text-slate-500">Current file: <span class="font-medium">{{ inv.voucher_name }}</span> — uploading a new one replaces it.</p>
                <p v-if="form.errors.voucher" class="err">{{ form.errors.voucher }}</p>
            </div>

            <div v-if="form.hasErrors" class="rounded-xl bg-red-50 px-4 py-3 text-sm text-red-600">Please fix the highlighted fields before saving.</div>
            <div class="flex items-center gap-3">
                <button type="submit" :disabled="form.processing" class="rounded-full bg-brand-gradient px-7 py-3 text-sm font-semibold text-white shadow-brand hover:opacity-90 disabled:opacity-50">{{ isEdit ? 'Save invoice' : 'Create invoice' }}</button>
                <Link href="/admin/invoices" class="text-sm text-slate-500 hover:text-brand-ink">Cancel</Link>
            </div>
        </form>
    </AdminLayout>
</template>

<style scoped>
.lbl { @apply mb-1.5 block text-sm font-medium text-slate-700; }
.inp { @apply w-full rounded-xl border-slate-200 text-sm focus:border-brand-purple focus:ring-brand-purple; }
.err { @apply mt-1 text-xs text-red-500; }
</style>
