<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'items' => 'array',
            'payments' => 'array',
            'issue_date' => 'date',
            'due_date' => 'date',
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
            'amount_paid' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(VisaApplication::class, 'visa_application_id');
    }

    /** Amount still owed. */
    public function balance(): float
    {
        return round((float) $this->total - (float) $this->amount_paid, 2);
    }

    /** Recalculate totals from line items + discount/tax, and derive status. */
    public function recalculate(): void
    {
        $subtotal = collect($this->items ?? [])->sum(
            fn ($i) => (float) ($i['qty'] ?? 0) * (float) ($i['unit_price'] ?? 0)
        );
        $this->subtotal = round($subtotal, 2);
        $this->total = round($subtotal - (float) $this->discount + (float) $this->tax, 2);

        // Keep an explicit "cancelled" state; otherwise derive from payment.
        if ($this->status !== 'cancelled') {
            if ($this->amount_paid <= 0) {
                $this->status = $this->status === 'sent' ? 'sent' : ($this->status ?: 'draft');
            } elseif ($this->balance() > 0.001) {
                $this->status = 'partial';
            } else {
                $this->status = 'paid';
            }
        }
    }

    /** Next sequential invoice number, e.g. INV-2026-0001. */
    public static function nextNumber(): string
    {
        $year = now()->year;
        $count = static::whereYear('created_at', $year)->count() + 1;

        return sprintf('INV-%d-%04d', $year, $count);
    }
}
