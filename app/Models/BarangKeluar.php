<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy(\App\Observers\BarangKeluarObserver::class)]
class BarangKeluar extends Model
{
    protected $table = 'barang_keluar';
    protected $guarded = [];

    protected $casts = [
        'aksesoris' => 'array',
        'tanggal_transaksi' => 'date',
        'tanggal_pengambilan' => 'date',
        'jumlah_lensa_pcs' => 'integer',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function frame()
    {
        return $this->belongsTo(Frame::class);
    }

    public function lens()
    {
        return $this->belongsTo(Lens::class);
    }
}
