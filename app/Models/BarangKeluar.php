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
        'tanggal_pelunasan' => 'date',
        'jumlah_lensa_pcs' => 'integer',
        'diskon' => 'decimal:2',
        'potongan_bpjs' => 'decimal:2',
        'sisa_bpjs' => 'decimal:2',
        'biaya_faset' => 'decimal:2',
        'biaya_beli_lensa' => 'decimal:2',
        'tambahan_biaya' => 'decimal:2',
        'total_aksesoris' => 'decimal:2',
        'biaya_beli_aksesoris' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::saving(function ($model) {
            if ($model->status_pembayaran === 'lunas') {
                if (is_null($model->tanggal_pelunasan)) {
                    if ($model->exists && $model->isDirty('status_pembayaran')) {
                        // Jika diubah dari DP/Belum Bayar ke Lunas (saat update)
                        $model->tanggal_pelunasan = now();
                    } else {
                        // Jika baru dibuat langsung lunas
                        $model->tanggal_pelunasan = $model->tanggal_transaksi ?? now();
                    }
                }
            } else {
                // Jika status diubah kembali ke dp/belum_bayar, kosongkan tanggal_pelunasan
                $model->tanggal_pelunasan = null;
            }
            
            // Bersihkan biaya_beli_lensa jika bukan Luar Optik
            if ($model->lens_id) {
                $lens = Lens::with('lensOwnershipCategory')->find($model->lens_id);
                if (!$lens || $lens->lensOwnershipCategory?->type !== 'Luar Optik') {
                    $model->biaya_beli_lensa = 0;
                }
            } else {
                $model->biaya_beli_lensa = 0;
            }
            
            // Hapus kolom lama biaya_faset
            $model->biaya_faset = 0;
        });
    }

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

    public function barangKeluarAccessories()
    {
        return $this->hasMany(BarangKeluarAccessory::class, 'barang_keluar_id');
    }
}
