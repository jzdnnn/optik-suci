<?php

namespace App\Http\Controllers;

use App\Models\BarangKeluar;
use App\Models\CabangOptik;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    /**
     * Kalkulasi data laporan keuangan harian.
     * Pengeluaran diambil dari tabel `pengeluaran` (Catat Pengeluaran),
     * difilter berdasarkan cabang dari session.
     * Saldo awal diambil dari field saldo_awal pada record CabangOptik.
     */
    public static function getDailyReportData(string $startDate, string $endDate): array
    {
        $start  = Carbon::parse($startDate)->startOfDay();
        $end    = Carbon::parse($endDate)->endOfDay();
        $cabang = session('cabang_nama'); // cabang dari session login

        // 1. Saldo awal: ambil dari record CabangOptik (bukan kalkulasi mundur)
        $cabangRecord   = $cabang ? CabangOptik::where('nama', $cabang)->first() : null;
        $runningBalance = $cabangRecord ? (float) $cabangRecord->saldo_awal : 0.0;
        $initialBalance = $runningBalance;

        // 2. Transaksi barang keluar per hari
        $transactions = BarangKeluar::whereBetween('tanggal_transaksi', [$start, $end])
            ->get()
            ->groupBy(fn ($item) => Carbon::parse($item->tanggal_transaksi)->toDateString());

        // 3. Pengeluaran per hari dari Catat Pengeluaran
        $expenseQuery = Pengeluaran::with('jenisPengeluaran')
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()]);
        if ($cabang) {
            $expenseQuery->where('cabang', $cabang);
        }
        $expenses = $expenseQuery->orderBy('tanggal')->get()
            ->groupBy(fn ($item) => Carbon::parse($item->tanggal)->toDateString());

        // 4. Generate date range
        $period = new \DatePeriod(
            $start->copy()->toDate(),
            new \DateInterval('P1D'),
            $end->copy()->addDay()->toDate()
        );

        $report = [];

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');

            if (Carbon::parse($dateStr)->isFuture()) {
                continue;
            }

            $dayTransactions = $transactions->get($dateStr, collect());
            $dayExpenses     = $expenses->get($dateStr, collect());

            // HARGA = total omzet (semua transaksi)
            $harga = $dayTransactions->sum('total_transaksi');

            // PENDAPATAN MASUK = kas yang benar-benar masuk
            $pendapatan = $dayTransactions->sum(function ($item) {
                return $item->status_pembayaran === 'lunas'
                    ? $item->total_transaksi
                    : ($item->status_pembayaran === 'dp' ? $item->dp_dibayar : 0);
            });

            // PENGELUARAN = detail per item dari Catat Pengeluaran
            $pengeluaranDetails = $dayExpenses->map(function ($exp) {
                $kategori = $exp->jenisPengeluaran?->nama ?? 'Pengeluaran';
                $label    = $exp->keterangan ? "{$kategori}: {$exp->keterangan}" : $kategori;
                return [
                    'label'   => $label,
                    'nominal' => (float) $exp->nominal,
                ];
            })->values()->toArray();

            $totalPengeluaran = collect($pengeluaranDetails)->sum('nominal');

            $runningBalance += ($pendapatan - $totalPengeluaran);

            $report[] = [
                'tanggal'             => Carbon::parse($dateStr)->translatedFormat('d.m.Y'),
                'tanggal_raw'         => $dateStr,
                'harga'               => (float) $harga,
                'pendapatan'          => (float) $pendapatan,
                'pengeluaran_details' => $pengeluaranDetails,
                'total_pengeluaran'   => (float) $totalPengeluaran,
                'saldo'               => (float) $runningBalance,
            ];
        }

        $filtered = array_filter($report, fn ($r) => $r['harga'] > 0 || $r['total_pengeluaran'] > 0);
        $filtered = array_values($filtered);

        return [
            'data'            => $filtered,
            'all_data'        => $report,
            'initial_balance' => $initialBalance,
            'totals'          => [
                'harga'       => collect($filtered)->sum('harga'),
                'pendapatan'  => collect($filtered)->sum('pendapatan'),
                'pengeluaran' => collect($filtered)->sum('total_pengeluaran'),
                'saldo'       => $runningBalance,
            ],
        ];
    }

    public function printDailyReport(Request $request)
    {
        if (!auth()->check() || !auth()->user()->can('viewAny_laporan_keuangan')) {
            abort(403, 'Unauthorized.');
        }

        $startDate = $request->query('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate   = $request->query('end_date', Carbon::now()->endOfMonth()->toDateString());

        $report = self::getDailyReportData($startDate, $endDate);

        return view('reports.print', [
            'report'          => $report['data'],
            'totals'          => $report['totals'],
            'initial_balance' => $report['initial_balance'],
            'startDate'       => Carbon::parse($startDate)->translatedFormat('d M Y'),
            'endDate'         => Carbon::parse($endDate)->translatedFormat('d M Y'),
            'cabang'          => session('cabang_nama', 'Suci Optikal'),
        ]);
    }
}
