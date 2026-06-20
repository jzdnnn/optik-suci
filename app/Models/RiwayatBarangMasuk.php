<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatBarangMasuk extends Model
{
    use HasFactory;
    protected $table = 'riwayat_barang_masuk';
    protected $guarded = [];
    public function barangMasuk() { return $this->belongsTo(BarangMasuk::class); }
}
