<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SetoranMingguan extends Model
{
    protected $table = 'setoran_mingguan';
    protected $guarded = [];

    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'decimal:2',
        'minggu_ke' => 'integer',
    ];
}
