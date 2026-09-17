<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->date('from') ?: now()->startOfYear();
        $to = $request->date('to') ?: now()->endOfDay();
        $from = Carbon::parse($from)->startOfDay();
        $to = Carbon::parse($to)->endOfDay();
        $type = $request->get('type');       // income | expense | null
        $category = $request->get('category');

        // Manual ledger rows
        $manual = Transaction::query()
            ->whereBetween('occurred_on', [$from->toDateTimeString(), $to->toDateTimeString()])
            ->when($type, fn ($q, $t) => $q->where('type', $t))
            ->when($category, fn ($q, $c) => $q->where('category', $c))
            ->orderByDesc('occurred_on')
            ->get()
            ->map(fn ($t) => [
                'id' => $t->id,
                'kind' => 'manual',
                'type' => $t->type,
                'category' => $t->category,
                'amount' => (float) $t->amount,
                'date' => $t->occurred_on?->format('Y-m-d'),
                'date_label' => $t->occurred_on?->format('d M Y'),
                'method' => $t->method,
                'note' => $t->note,
                'editable' => true,
            ]);

        // Invoice payments as read-only income (unless filtering expenses/other category)
        $invoiceIncome = collect();
        if (($type === null || $type === 'income') && ($category === null || $category === 'Invoice payment')) {
            $invoiceIncome = $this->invoicePayments($from, $to);
        }

        $rows = $manual->concat($invoiceIncome)->sortByDesc('date')->values();

        // Summary (whole range, independent of pagination)
        $income = $this->sum($rows, 'income');
        $expense = $this->sum($rows, 'expense');

        return Inertia::render('Admin/Finance/Index', [
            'transactions' => $this->paginate($rows, $request, 20),
            'summary' => [
                'income' => $income,
                'expense' => $expense,
                'net' => round($income - $expense, 2),
            ],
            'chart' => $this->monthly($from, $to),
            'byCategory' => $this->byCategory($rows),
            'filters' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
                'type' => $type,
                'category' => $category,
            ],
            'categories' => [
                'income' => Transaction::INCOME_CATEGORIES,
                'expense' => Transaction::EXPENSE_CATEGORIES,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Finance/Form', [
            'transaction' => null,
            'categories' => [
                'income' => Transaction::INCOME_CATEGORIES,
                'expense' => Transaction::EXPENSE_CATEGORIES,
            ],
        ]);
    }

    public function edit(Transaction $transaction)
    {
        return Inertia::render('Admin/Finance/Form', [
            'transaction' => [
                'id' => $transaction->id,
                'type' => $transaction->type,
                'category' => $transaction->category,
                'amount' => (float) $transaction->amount,
                'occurred_on' => $transaction->occurred_on?->format('Y-m-d'),
                'method' => $transaction->method,
                'reference' => $transaction->reference,
                'note' => $transaction->note,
            ],
            'categories' => [
                'income' => Transaction::INCOME_CATEGORIES,
                'expense' => Transaction::EXPENSE_CATEGORIES,
            ],
        ]);
    }

    public function store(Request $request)
    {
        Transaction::create($this->validated($request));

        return redirect()->route('admin.finance.index')->with('success', 'Transaction recorded.');
    }

    public function update(Request $request, Transaction $transaction)
    {
        $transaction->update($this->validated($request));

        return redirect()->route('admin.finance.index')->with('success', 'Transaction updated.');
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();

        return back()->with('success', 'Transaction deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'type' => ['required', Rule::in(['income', 'expense'])],
            'category' => ['required', 'string', 'max:80'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'occurred_on' => ['required', 'date'],
            'method' => ['nullable', 'string', 'max:40'],
            'reference' => ['nullable', 'string', 'max:80'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);
    }

    /** Invoice payments in range, shaped like ledger income rows. */
    private function invoicePayments(Carbon $from, Carbon $to): Collection
    {
        $rows = collect();
        Invoice::query()->whereNotNull('payments')->get(['id', 'number', 'payments'])->each(function ($inv) use (&$rows, $from, $to) {
            foreach ($inv->payments ?? [] as $i => $p) {
                $date = $p['date'] ?? null;
                if (! $date) {
                    continue;
                }
                try {
                    $d = Carbon::parse($date);
                } catch (\Throwable $e) {
                    continue;
                }
                if ($d->lt($from) || $d->gt($to)) {
                    continue;
                }
                $rows->push([
                    'id' => 'inv-'.$inv->id.'-'.$i,
                    'kind' => 'invoice',
                    'type' => 'income',
                    'category' => 'Invoice payment',
                    'amount' => (float) ($p['amount'] ?? 0),
                    'date' => $d->format('Y-m-d'),
                    'date_label' => $d->format('d M Y'),
                    'method' => $p['method'] ?? null,
                    'note' => 'Invoice '.$inv->number,
                    'url' => route('admin.invoices.show', $inv->id),
                    'editable' => false,
                ]);
            }
        });

        return $rows;
    }

    private function sum(Collection $rows, string $type): float
    {
        return round((float) $rows->where('type', $type)->sum('amount'), 2);
    }

    /** Top expense/income categories in range. */
    private function byCategory(Collection $rows): array
    {
        return $rows->groupBy('category')->map(fn ($g, $cat) => [
            'category' => $cat,
            'type' => $g->first()['type'],
            'total' => round((float) $g->sum('amount'), 2),
        ])->sortByDesc('total')->values()->take(8)->all();
    }

    /** Monthly income vs expense for the range (most recent 12 months). */
    private function monthly(Carbon $from, Carbon $to): array
    {
        $months = [];
        $cursor = $from->copy()->startOfMonth();
        $end = $to->copy()->startOfMonth();
        while ($cursor->lte($end)) {
            $months[$cursor->format('Y-m')] = ['month' => $cursor->format('M Y'), 'income' => 0.0, 'expense' => 0.0];
            $cursor->addMonth();
        }
        $months = array_slice($months, -12, 12, true);

        // Manual transactions
        Transaction::query()
            ->whereBetween('occurred_on', [$from->toDateTimeString(), $to->toDateTimeString()])
            ->get(['type', 'amount', 'occurred_on'])
            ->each(function ($t) use (&$months) {
                $k = $t->occurred_on?->format('Y-m');
                if ($k && isset($months[$k])) {
                    $months[$k][$t->type] += (float) $t->amount;
                }
            });

        // Invoice payments as income
        $this->invoicePayments($from, $to)->each(function ($p) use (&$months) {
            $k = Carbon::parse($p['date'])->format('Y-m');
            if (isset($months[$k])) {
                $months[$k]['income'] += $p['amount'];
            }
        });

        return array_map(fn ($m) => [
            'month' => $m['month'],
            'income' => round($m['income'], 2),
            'expense' => round($m['expense'], 2),
        ], array_values($months));
    }

    /** Turn a merged collection into a paginated payload. */
    private function paginate(Collection $rows, Request $request, int $perPage): array
    {
        $page = max(1, (int) $request->get('page', 1));
        $items = $rows->forPage($page, $perPage)->values();
        $p = new LengthAwarePaginator($items, $rows->count(), $perPage, $page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        return [
            'data' => $items,
            'links' => $p->linkCollection()->toArray(),
            'total' => $rows->count(),
        ];
    }
}
