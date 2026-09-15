<?php

namespace App\Models;

use App\Models\VisaCountry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisaApplication extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'travel_date' => 'date',
            'date_of_birth' => 'date',
            'passport_expiry' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Human-friendly country name from the visa config.
     */
    public function countryName(): string
    {
        return data_get(VisaCountry::config(), "{$this->country}.name", ucfirst($this->country));
    }

    public function countryFlag(): string
    {
        return data_get(VisaCountry::config(), "{$this->country}.flag", '');
    }
}
