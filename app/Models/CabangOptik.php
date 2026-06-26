<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CabangOptik extends Model
{
    protected $table = 'cabang_optik';
    protected $guarded = [];

    protected $casts = [
        'is_active'  => 'boolean',
        'saldo_awal' => 'decimal:2',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
