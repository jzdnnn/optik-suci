<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lens extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function lensOwnershipCategory() { return $this->belongsTo(LensOwnershipCategory::class); }
    public function barangMasuk() { return $this->morphOne(BarangMasuk::class, 'barang_masukable'); }
}
