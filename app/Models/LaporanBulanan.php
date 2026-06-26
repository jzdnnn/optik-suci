<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanBulanan extends Model
{
    protected $table = 'laporan_bulanan';
    protected $guarded = [];

    protected $casts = [
        'selisih_details' => 'array',
        'omzet' => 'decimal:2',
        'pendapatan_bpjs' => 'decimal:2',
        'pendapatan_harian' => 'decimal:2',
        'setoran_minggu_1' => 'decimal:2',
        'setoran_minggu_2' => 'decimal:2',
        'setoran_minggu_3' => 'decimal:2',
        'setoran_minggu_4' => 'decimal:2',
        'setoran_minggu_5' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::saving(function ($model) {
            $model->omzet = $model->calculated_omzet;
            $model->pendapatan_harian = $model->calculated_pendapatan_harian;

            // Calculate weekly deposits
            for ($week = 1; $week <= 5; $week++) {
                $prop = "setoran_minggu_{$week}";
                $model->$prop = (float) SetoranMingguan::where('cabang', $model->cabang)
                    ->whereYear('tanggal', $model->tahun)
                    ->whereMonth('tanggal', $model->bulan)
                    ->where('minggu_ke', $week)
                    ->sum('nominal');
            }
        });
    }

    public function getCalculatedOmzetAttribute(): float
    {
        // Omset dari transaksi yang dibuat pada bulan ini
        $omzetBulanIni = BarangKeluar::whereYear('tanggal_transaksi', $this->tahun)
            ->whereMonth('tanggal_transaksi', $this->bulan)
            ->get()
            ->sum(function ($item) {
                // Jika langsung lunas di hari yang sama
                if ($item->status_pembayaran === 'lunas' && $item->tanggal_pelunasan == $item->tanggal_transaksi) {
                    return $item->total_transaksi;
                }
                // Jika DP (termasuk yang sudah lunas tapi pelunasannya di hari/bulan berbeda), 
                // maka cash yang masuk pada saat tanggal_transaksi adalah sebesar DP
                return $item->dp_dibayar ?: 0;
            });

        // Omset dari pelunasan transaksi yang dibuat di bulan sebelumnya namun dilunasi bulan ini
        $pelunasanBulanIni = BarangKeluar::whereYear('tanggal_pelunasan', $this->tahun)
            ->whereMonth('tanggal_pelunasan', $this->bulan)
            ->whereColumn('tanggal_pelunasan', '!=', 'tanggal_transaksi')
            ->get()
            ->sum(function ($item) {
                return $item->total_transaksi - ($item->dp_dibayar ?: 0);
            });

        return (float) ($omzetBulanIni + $pelunasanBulanIni);
    }

    public function getCalculatedPendapatanHarianAttribute(): float
    {
        // Dalam konteks baru, pendapatan harian (cash masuk harian dari transaksi)
        // nilainya ekuivalen dengan omset (cash basis). Kita return nilai yang sama.
        return $this->getCalculatedOmzetAttribute();
    }

    public function getTotalPendapatanAttribute(): float
    {
        return (float) ($this->calculated_omzet + $this->pendapatan_bpjs + $this->calculated_pendapatan_harian);
    }

    public function getTotalSelisihAttribute(): float
    {
        $details = $this->selisih_details ?? [];
        return (float) collect($details)->sum(fn ($item) => (float) ($item['nominal'] ?? 0));
    }

    public function getTotalSetoranBulananAttribute(): float
    {
        return (float) (
            $this->setoran_minggu_1 +
            $this->setoran_minggu_2 +
            $this->setoran_minggu_3 +
            $this->setoran_minggu_4 +
            $this->setoran_minggu_5
        );
    }

    public function getExpenseSumByType(string $type): float
    {
        return (float) Pengeluaran::join('jenis_pengeluaran', 'pengeluaran.jenis_pengeluaran_id', '=', 'jenis_pengeluaran.id')
            ->where('pengeluaran.cabang', $this->cabang)
            ->whereYear('pengeluaran.tanggal', $this->tahun)
            ->whereMonth('pengeluaran.tanggal', $this->bulan)
            ->where('jenis_pengeluaran.tipe', $type)
            ->sum('pengeluaran.nominal');
    }

    public function getExpensesByType(string $type)
    {
        return Pengeluaran::join('jenis_pengeluaran', 'pengeluaran.jenis_pengeluaran_id', '=', 'jenis_pengeluaran.id')
            ->where('pengeluaran.cabang', $this->cabang)
            ->whereYear('pengeluaran.tanggal', $this->tahun)
            ->whereMonth('pengeluaran.tanggal', $this->bulan)
            ->where('jenis_pengeluaran.tipe', $type)
            ->selectRaw('jenis_pengeluaran.nama, SUM(pengeluaran.nominal) as total_nominal, COUNT(pengeluaran.id) as count, GROUP_CONCAT(pengeluaran.keterangan SEPARATOR ", ") as keterangan_list')
            ->groupBy('jenis_pengeluaran.nama')
            ->get();
    }

    public function getTotalPengeluaranOperasionalAttribute(): float
    {
        return $this->getExpenseSumByType('operasional');
    }

    public function getTotalPengeluaranStokAttribute(): float
    {
        return $this->getExpenseSumByType('stok');
    }

    public function getTotalPengeluaranGajiAttribute(): float
    {
        return $this->getExpenseSumByType('gaji');
    }

    public function getTotalSeluruhPengeluaranAttribute(): float
    {
        return (float) (
            $this->total_pengeluaran_operasional +
            $this->total_pengeluaran_stok +
            $this->total_pengeluaran_gaji
        );
    }

    public function getLabaBersihAttribute(): float
    {
        return (float) (
            $this->total_pendapatan -
            $this->total_seluruh_pengeluaran -
            $this->total_selisih
        );
    }

    public function getNamaBulanAttribute(): string
    {
        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        return $months[$this->bulan] ?? '';
    }
}
