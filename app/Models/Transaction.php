<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $guarded = ['id'];

    public const INCOME_CATEGORIES = [
        'Visa service', 'Air ticket', 'Tour package', 'Service charge', 'Other income',
    ];

    public const EXPENSE_CATEGORIES = [
        'Supplier / airline', 'Embassy / govt fee', 'Salaries', 'Office rent',
        'Utilities', 'Marketing', 'Refund', 'Bank charge', 'Other expense',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'occurred_on' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeIncome($q)
    {
        return $q->where('type', 'income');
    }

    public function scopeExpense($q)
    {
        return $q->where('type', 'expense');
    }
}
