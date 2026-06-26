<?php

namespace App\Filament\Admin\Widgets;

use App\Models\BarangKeluar;
use App\Models\RiwayatBarangMasuk;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class IncomeExpenseChart extends ChartWidget
{
    protected ?string $heading = 'Grafik Keuangan (Pemasukan vs Pengeluaran)';
    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $currentYear = Carbon::now()->year;

        $incomeData = [];
        $expenseData = [];

        // Pre-fetch transactions for the current year
        $transactions = BarangKeluar::whereYear('tanggal_transaksi', $currentYear)->get();

        // Pre-fetch expenses for the current year
        $riwayats = RiwayatBarangMasuk::where('jenis_pergerakan', 'masuk')
            ->whereYear('tanggal', $currentYear)
            ->with(['barangMasuk.barangMasukable'])
            ->get();

        for ($month = 1; $month <= 12; $month++) {
            // Calculate Income (Pemasukan)
            $income = $transactions->filter(fn ($t) => Carbon::parse($t->tanggal_transaksi)->month === $month)
                ->sum('total_transaksi');
            $incomeData[] = $income;

            // Calculate Expense (Pengeluaran)
            $expense = $riwayats->filter(fn ($r) => Carbon::parse($r->tanggal)->month === $month)
                ->sum(function ($item) {
                    $barang = $item->barangMasuk?->barangMasukable;
                    $hargaBeli = $barang?->harga_beli ?? 0;
                    return $item->jumlah * $hargaBeli;
                });
            $expenseData[] = $expense;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pemasukan (Rp)',
                    'data' => $incomeData,
                    'borderColor' => '#10B981', // Emerald green
                    'backgroundColor' => 'rgba(16, 185, 129, 0.2)',
                    'fill' => true,
                ],
                [
                    'label' => 'Pengeluaran (Rp)',
                    'data' => $expenseData,
                    'borderColor' => '#EF4444', // Red
                    'backgroundColor' => 'rgba(239, 68, 68, 0.2)',
                    'fill' => true,
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
