<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'includes' => 'array',
            'active' => 'boolean',
        ];
    }

    public function scopeActiveOrdered($query)
    {
        return $query->where('active', true)->orderBy('sort_order')->orderBy('title');
    }
}
