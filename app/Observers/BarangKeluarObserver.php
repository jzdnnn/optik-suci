<?php

namespace App\Observers;

use App\Models\BarangKeluar;
use App\Models\BarangMasuk;
use App\Models\RiwayatBarangMasuk;

class BarangKeluarObserver
{
    public function creating(BarangKeluar $barangKeluar): void
    {
        if (empty($barangKeluar->no_bon)) {
            $lastBarangKeluar = BarangKeluar::orderBy('id', 'desc')->first();
            $lastNumber = 0;
            
            if ($lastBarangKeluar && $lastBarangKeluar->no_bon) {
                $lastNumber = (int) $lastBarangKeluar->no_bon;
            }
            
            $newNumber = $lastNumber + 1;
            $barangKeluar->no_bon = str_pad($newNumber, 3, '0', STR_PAD_LEFT);
        }
    }

    public function created(BarangKeluar $barangKeluar): void
    {
        if (in_array($barangKeluar->tipe_transaksi, ['frame', 'lengkap']) && $barangKeluar->frame_id) {
            $frame = $barangKeluar->frame;
            if ($frame && $frame->barangMasuk) {
                $barangMasuk = $frame->barangMasuk;
                $barangMasuk->stok -= 1;
                $barangMasuk->save();

                RiwayatBarangMasuk::create([
                    'barang_masuk_id' => $barangMasuk->id,
                    'jenis_pergerakan' => 'keluar',
                    'jumlah' => 1,
                    'keterangan' => 'Barang Keluar #' . $barangKeluar->id,
                    'tanggal' => now(),
                ]);
            }
        }

        if (in_array($barangKeluar->tipe_transaksi, ['lensa', 'lengkap']) && $barangKeluar->lens_id) {
            $lens = $barangKeluar->lens;
            if ($lens && $lens->barangMasuk) {
                $barangMasuk = $lens->barangMasuk;
                $pcs = $barangKeluar->jumlah_lensa_pcs ?: 2; // default 1 pasang = 2 pcs
                $barangMasuk->stok -= $pcs;
                $barangMasuk->save();

                RiwayatBarangMasuk::create([
                    'barang_masuk_id' => $barangMasuk->id,
                    'jenis_pergerakan' => 'keluar',
                    'jumlah' => $pcs,
                    'keterangan' => 'Barang Keluar #' . $barangKeluar->id . ' (' . $pcs . ' pcs lensa)',
                    'tanggal' => now(),
                ]);
            }
        }
    }

    public function updated(BarangKeluar $barangKeluar): void
    {
        //
    }

    public function saved(BarangKeluar $barangKeluar): void
    {
        $accessories = $barangKeluar->barangKeluarAccessories()->get();
        $totalJual = (float) $accessories->sum('subtotal_jual');
        $totalBeli = (float) $accessories->sum('subtotal_beli');

        if ((float)$barangKeluar->total_aksesoris !== $totalJual || (float)$barangKeluar->biaya_beli_aksesoris !== $totalBeli) {
            \Illuminate\Support\Facades\DB::table('barang_keluar')
                ->where('id', $barangKeluar->id)
                ->update([
                    'total_aksesoris' => $totalJual,
                    'biaya_beli_aksesoris' => $totalBeli,
                ]);
        }
    }

    public function deleted(BarangKeluar $barangKeluar): void
    {
        if (in_array($barangKeluar->tipe_transaksi, ['frame', 'lengkap']) && $barangKeluar->frame_id) {
            $frame = $barangKeluar->frame;
            if ($frame && $frame->barangMasuk) {
                $barangMasuk = $frame->barangMasuk;
                $barangMasuk->stok += 1;
                $barangMasuk->save();

                RiwayatBarangMasuk::create([
                    'barang_masuk_id' => $barangMasuk->id,
                    'jenis_pergerakan' => 'masuk',
                    'jumlah' => 1,
                    'keterangan' => 'Pembatalan Barang Keluar #' . $barangKeluar->id,
                    'tanggal' => now(),
                ]);
            }
        }

        if (in_array($barangKeluar->tipe_transaksi, ['lensa', 'lengkap']) && $barangKeluar->lens_id) {
            $lens = $barangKeluar->lens;
            if ($lens && $lens->barangMasuk) {
                $barangMasuk = $lens->barangMasuk;
                $pcs = $barangKeluar->jumlah_lensa_pcs ?: 2;
                $barangMasuk->stok += $pcs;
                $barangMasuk->save();

                RiwayatBarangMasuk::create([
                    'barang_masuk_id' => $barangMasuk->id,
                    'jenis_pergerakan' => 'masuk',
                    'jumlah' => $pcs,
                    'keterangan' => 'Pembatalan Barang Keluar #' . $barangKeluar->id . ' (' . $pcs . ' pcs lensa)',
                    'tanggal' => now(),
                ]);
            }
        }
    }

    public function restored(BarangKeluar $barangKeluar): void
    {
        //
    }

    public function forceDeleted(BarangKeluar $barangKeluar): void
    {
        //
    }
}
