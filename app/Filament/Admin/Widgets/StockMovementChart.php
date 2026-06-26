<?php

namespace App\Filament\Admin\Widgets;

use App\Models\RiwayatBarangMasuk;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class StockMovementChart extends ChartWidget
{
    protected ?string $heading = 'Grafik Pergerakan Stok (Barang Masuk vs Barang Keluar)';
    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $currentYear = Carbon::now()->year;

        $masukData = [];
        $keluarData = [];

        // Pre-fetch stock histories for the current year
        $histories = RiwayatBarangMasuk::whereYear('tanggal', $currentYear)->get();

        for ($month = 1; $month <= 12; $month++) {
            $masuk = $histories->filter(fn ($h) => $h->jenis_pergerakan === 'masuk' && Carbon::parse($h->tanggal)->month === $month)
                ->sum('jumlah');
            $masukData[] = $masuk;

            $keluar = $histories->filter(fn ($h) => $h->jenis_pergerakan === 'keluar' && Carbon::parse($h->tanggal)->month === $month)
                ->sum('jumlah');
            $keluarData[] = $keluar;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Barang Masuk (Pcs)',
                    'data' => $masukData,
                    'backgroundColor' => '#3B82F6', // Blue
                ],
                [
                    'label' => 'Barang Keluar (Pcs)',
                    'data' => $keluarData,
                    'backgroundColor' => '#F59E0B', // Amber
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
