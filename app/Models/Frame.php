<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Frame extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function frameCategory() { return $this->belongsTo(FrameCategory::class); }
    public function barangMasuk() { return $this->morphOne(BarangMasuk::class, 'barang_masukable'); }
}
