<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    transaction: { type: Object, default: null },
    categories: Object,
});

const isEdit = !!props.transaction;
const tx = props.transaction || {};
const today = new Date().toISOString().slice(0, 10);

const form = useForm({
    type: tx.type || 'expense',
    category: tx.category || '',
    amount: tx.amount != null ? Number(tx.amount) : null,
    occurred_on: tx.occurred_on || today,
    method: tx.method || '',
    reference: tx.reference || '',
    note: tx.note || '',
});

const catList = computed(() => (form.type === 'income' ? props.categories.income : props.categories.expense));

function submit() {
    const opts = { preserveScroll: true };
    isEdit ? form.put(route('admin.finance.update', props.transaction.id), opts) : form.post(route('admin.finance.store'), opts);
}
</script>

<template>
    <Head :title="isEdit ? 'Edit transaction' : 'New transaction'" />
    <AdminLayout>
        <template #title>{{ isEdit ? 'Edit transaction' : 'New transaction' }}</template>

        <Link href="/admin/finance" class="text-sm font-medium text-slate-500 hover:text-brand-purple">← Back to finance</Link>

        <form @submit.prevent="submit" class="mt-4 max-w-2xl space-y-6">
            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <!-- Type toggle -->
                <div class="flex gap-2">
                    <button type="button" @click="form.type = 'income'; form.category = ''"
                        class="flex-1 rounded-xl border px-4 py-3 text-sm font-semibold transition"
                        :class="form.type === 'income' ? 'border-emerald-500 bg-emerald-50 text-emerald-700' : 'border-slate-200 bg-white text-slate-500 hover:bg-slate-50'">
                        Income
                    </button>
                    <button type="button" @click="form.type = 'expense'; form.category = ''"
                        class="flex-1 rounded-xl border px-4 py-3 text-sm font-semibold transition"
                        :class="form.type === 'expense' ? 'border-red-400 bg-red-50 text-red-600' : 'border-slate-200 bg-white text-slate-500 hover:bg-slate-50'">
                        Expense
                    </button>
                </div>

                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="lbl">Category</label>
                        <input v-model="form.category" list="catlist" class="inp" placeholder="Choose or type…" />
                        <datalist id="catlist"><option v-for="c in catList" :key="c" :value="c" /></datalist>
                        <p v-if="form.errors.category" class="err">{{ form.errors.category }}</p>
                    </div>
                    <div><label class="lbl">Amount (৳)</label><input v-model.number="form.amount" type="number" step="0.01" min="0.01" class="inp" /><p v-if="form.errors.amount" class="err">{{ form.errors.amount }}</p></div>
                    <div><label class="lbl">Date</label><input v-model="form.occurred_on" type="date" class="inp" /><p v-if="form.errors.occurred_on" class="err">{{ form.errors.occurred_on }}</p></div>
                    <div><label class="lbl">Method <span class="text-slate-400">(optional)</span></label><input v-model="form.method" class="inp" placeholder="Cash / Bank / bKash" /></div>
                    <div><label class="lbl">Reference <span class="text-slate-400">(optional)</span></label><input v-model="form.reference" class="inp" placeholder="Voucher / txn id" /></div>
                    <div class="sm:col-span-2"><label class="lbl">Note <span class="text-slate-400">(optional)</span></label><textarea v-model="form.note" rows="2" class="inp"></textarea></div>
                </div>
            </div>

            <div v-if="form.hasErrors" class="rounded-xl bg-red-50 px-4 py-3 text-sm text-red-600">Please fix the highlighted fields.</div>
            <div class="flex items-center gap-3">
                <button type="submit" :disabled="form.processing" class="rounded-full bg-brand-gradient px-7 py-3 text-sm font-semibold text-white shadow-brand hover:opacity-90 disabled:opacity-50">{{ isEdit ? 'Save' : 'Record transaction' }}</button>
                <Link href="/admin/finance" class="text-sm text-slate-500 hover:text-brand-ink">Cancel</Link>
            </div>
        </form>
    </AdminLayout>
</template>

<style scoped>
.lbl { @apply mb-1.5 block text-sm font-medium text-slate-700; }
.inp { @apply w-full rounded-xl border-slate-200 text-sm focus:border-brand-purple focus:ring-brand-purple; }
.err { @apply mt-1 text-xs text-red-500; }
</style>
