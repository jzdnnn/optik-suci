<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accessory extends Model
{
    use HasFactory;

    protected $table = 'accessories';
    protected $guarded = [];

    protected $casts = [
        'stok' => 'integer',
        'harga_beli' => 'decimal:2',
        'harga_jual' => 'decimal:2',
    ];
}
