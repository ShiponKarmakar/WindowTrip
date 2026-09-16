<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlightTicket extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'passengers' => 'array',
            'segments' => 'array',
            'issue_date' => 'date',
            'fare_total' => 'decimal:2',
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

    /** First segment's departure, used for list sorting / display. */
    public function firstDeparture(): ?string
    {
        return $this->segments[0]['depart_at'] ?? null;
    }

    /** Compact route summary, e.g. "DAC → DXB → LHR". */
    public function routeSummary(): string
    {
        $codes = [];
        foreach ($this->segments ?? [] as $s) {
            if (empty($codes) && ! empty($s['from_code'])) {
                $codes[] = strtoupper($s['from_code']);
            }
            if (! empty($s['to_code'])) {
                $codes[] = strtoupper($s['to_code']);
            }
        }

        return implode(' → ', $codes);
    }

    /** Next sequential ticket number, e.g. WT-TKT-2026-0001. */
    public static function nextNumber(): string
    {
        $year = now()->year;
        $count = static::whereYear('created_at', $year)->count() + 1;

        return sprintf('WT-TKT-%d-%04d', $year, $count);
    }
}
