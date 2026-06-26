<?php

namespace App\Filament\Admin\Widgets;

use App\Models\BarangKeluar;
use App\Models\Patient;
use App\Models\RiwayatBarangMasuk;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalPatients = Patient::count();
        $totalPemasukan = BarangKeluar::sum('total_transaksi');

        $riwayats = RiwayatBarangMasuk::where('jenis_pergerakan', 'masuk')
            ->with(['barangMasuk.barangMasukable'])
            ->get();

        $totalPengeluaran = $riwayats->sum(function ($item) {
            $barang = $item->barangMasuk?->barangMasukable;
            $hargaBeli = $barang?->harga_beli ?? 0;
            return $item->jumlah * $hargaBeli;
        });

        return [
            Stat::make('Total Customer', $totalPatients)
                ->description('Jumlah pasien terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
            Stat::make('Total Pemasukan', 'Rp ' . number_format($totalPemasukan, 0, ',', '.'))
                ->description('Total dari transaksi penjualan')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Total Pengeluaran', 'Rp ' . number_format($totalPengeluaran, 0, ',', '.'))
                ->description('Total biaya pembelian stok barang')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),
        ];
    }
}
