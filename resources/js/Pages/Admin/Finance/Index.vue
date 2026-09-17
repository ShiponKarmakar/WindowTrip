<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    transactions: Object,
    summary: Object,
    chart: { type: Array, default: () => [] },
    byCategory: { type: Array, default: () => [] },
    filters: Object,
    categories: Object,
});

const from = ref(props.filters.from);
const to = ref(props.filters.to);
const type = ref(props.filters.type || '');
const category = ref(props.filters.category || '');

function apply() {
    router.get('/admin/finance', {
        from: from.value || undefined,
        to: to.value || undefined,
        type: type.value || undefined,
        category: category.value || undefined,
    }, { preserveState: true, replace: true });
}

const money = (n) => '৳' + Number(n || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });

const chartMax = computed(() => Math.max(1, ...props.chart.map((m) => Math.max(m.income, m.expense))));

const catOptions = computed(() => {
    if (type.value === 'income') return props.categories.income;
    if (type.value === 'expense') return props.categories.expense;
    return [...props.categories.income, ...props.categories.expense, 'Invoice payment'];
});

function remove(t) {
    if (t.kind !== 'manual') return;
    if (confirm('Delete this transaction? This cannot be undone.')) {
        router.delete(route('admin.finance.destroy', t.id), { preserveScroll: true });
    }
}
</script>

<template>
    <Head title="Finance" />
    <AdminLayout>
        <template #title>Finance</template>

        <!-- KPI cards -->
        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                <div class="text-sm text-slate-500">Income</div>
                <div class="mt-1 font-heading text-2xl font-bold text-emerald-600">{{ money(summary.income) }}</div>
            </div>
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                <div class="text-sm text-slate-500">Expenses</div>
                <div class="mt-1 font-heading text-2xl font-bold text-red-500">{{ money(summary.expense) }}</div>
            </div>
            <div class="rounded-2xl bg-brand-gradient p-5 text-white shadow-brand">
                <div class="text-sm text-white/80">Net profit</div>
                <div class="mt-1 font-heading text-2xl font-bold">{{ money(summary.net) }}</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="mt-5 flex flex-wrap items-end gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
            <div><label class="lbl">From</label><input v-model="from" type="date" class="inp" /></div>
            <div><label class="lbl">To</label><input v-model="to" type="date" class="inp" /></div>
            <div>
                <label class="lbl">Type</label>
                <select v-model="type" class="inp capitalize"><option value="">All</option><option value="income">Income</option><option value="expense">Expense</option></select>
            </div>
            <div>
                <label class="lbl">Category</label>
                <select v-model="category" class="inp"><option value="">All</option><option v-for="c in catOptions" :key="c" :value="c">{{ c }}</option></select>
            </div>
            <button @click="apply" class="rounded-xl bg-brand-ink px-5 py-2.5 text-sm font-semibold text-white hover:opacity-90">Apply</button>
            <Link :href="route('admin.finance.create')" class="ml-auto rounded-full bg-brand-gradient px-5 py-2.5 text-sm font-semibold text-white shadow-brand hover:opacity-90">+ New transaction</Link>
        </div>

        <div class="mt-5 grid gap-5 lg:grid-cols-3">
            <!-- Monthly chart -->
            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm lg:col-span-2">
                <div class="flex items-center justify-between">
                    <h3 class="font-heading font-semibold text-brand-ink">Income vs expenses</h3>
                    <div class="flex items-center gap-4 text-xs text-slate-500">
                        <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>Income</span>
                        <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-red-400"></span>Expense</span>
                    </div>
                </div>
                <div v-if="chart.length" class="mt-6 flex h-48 items-end gap-3 overflow-x-auto">
                    <div v-for="(m, i) in chart" :key="i" class="flex min-w-[42px] flex-1 flex-col items-center gap-2">
                        <div class="flex h-40 w-full items-end justify-center gap-1">
                            <div class="w-1/2 rounded-t bg-emerald-500/90" :style="{ height: (m.income / chartMax * 100) + '%' }" :title="'Income ' + money(m.income)"></div>
                            <div class="w-1/2 rounded-t bg-red-400/90" :style="{ height: (m.expense / chartMax * 100) + '%' }" :title="'Expense ' + money(m.expense)"></div>
                        </div>
                        <div class="whitespace-nowrap text-[10px] text-slate-400">{{ m.month }}</div>
                    </div>
                </div>
                <p v-else class="mt-6 text-sm text-slate-400">No data for this range.</p>
            </div>

            <!-- Category breakdown -->
            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <h3 class="font-heading font-semibold text-brand-ink">Top categories</h3>
                <div v-if="byCategory.length" class="mt-4 space-y-2.5">
                    <div v-for="c in byCategory" :key="c.category" class="flex items-center justify-between text-sm">
                        <span class="flex items-center gap-2 text-slate-600">
                            <span class="h-2 w-2 rounded-full" :class="c.type === 'income' ? 'bg-emerald-500' : 'bg-red-400'"></span>
                            {{ c.category }}
                        </span>
                        <span class="font-medium text-brand-ink">{{ money(c.total) }}</span>
                    </div>
                </div>
                <p v-else class="mt-4 text-sm text-slate-400">No transactions yet.</p>
            </div>
        </div>

        <!-- Ledger -->
        <div class="mt-5 overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-400">
                    <tr>
                        <th class="px-5 py-3">Date</th><th class="px-5 py-3">Type</th>
                        <th class="px-5 py-3">Category</th><th class="px-5 py-3">Note</th>
                        <th class="px-5 py-3 text-right">Amount</th><th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-for="t in transactions.data" :key="t.id" class="hover:bg-slate-50">
                        <td class="px-5 py-3 text-slate-500">{{ t.date_label }}</td>
                        <td class="px-5 py-3">
                            <span class="rounded-full px-2.5 py-1 text-xs font-medium capitalize" :class="t.type === 'income' ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-500'">{{ t.type }}</span>
                        </td>
                        <td class="px-5 py-3 text-brand-ink">{{ t.category }}</td>
                        <td class="px-5 py-3 text-slate-500">
                            <span v-if="t.kind === 'invoice'">{{ t.note }}</span>
                            <span v-else>{{ t.note || '—' }}</span>
                            <span v-if="t.method" class="text-slate-400"> · {{ t.method }}</span>
                        </td>
                        <td class="px-5 py-3 text-right font-medium" :class="t.type === 'income' ? 'text-emerald-600' : 'text-red-500'">
                            {{ t.type === 'income' ? '+' : '−' }}{{ money(t.amount) }}
                        </td>
                        <td class="px-5 py-3 text-right">
                            <template v-if="t.editable">
                                <Link :href="route('admin.finance.edit', t.id)" class="text-xs font-semibold text-brand-purple hover:underline">Edit</Link>
                                <button @click="remove(t)" class="ml-3 text-xs font-semibold text-red-400 hover:text-red-600">Delete</button>
                            </template>
                            <a v-else-if="t.url" :href="t.url" class="text-xs font-semibold text-slate-400 hover:text-brand-purple">Invoice →</a>
                        </td>
                    </tr>
                    <tr v-if="!transactions.data.length"><td colspan="6" class="px-5 py-12 text-center text-slate-400">No transactions in this range. Add one to get started.</td></tr>
                </tbody>
            </table>
        </div>

        <div v-if="transactions.links.length > 3" class="mt-5 flex flex-wrap gap-1">
            <Link v-for="link in transactions.links" :key="link.label" :href="link.url || ''" v-html="link.label"
                class="rounded-lg px-3.5 py-2 text-sm"
                :class="[link.active ? 'bg-brand-gradient text-white' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50', !link.url && 'pointer-events-none opacity-40']" />
        </div>
    </AdminLayout>
</template>

<style scoped>
.lbl { @apply mb-1 block text-xs font-medium text-slate-500; }
.inp { @apply rounded-xl border-slate-200 text-sm focus:border-brand-purple focus:ring-brand-purple; }
</style>
