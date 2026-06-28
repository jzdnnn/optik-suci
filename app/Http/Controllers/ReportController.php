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

        // 1. Saldo awal: akumulasi saldo_awal + (Pemasukan - Pengeluaran) sebelum tanggal mulai
        $runningBalance = self::getRunningBalanceBeforeDate($start, $cabang);
        $initialBalance = $runningBalance;

        // 2. Transaksi barang keluar per hari (berdasarkan tanggal transaksi dibuat)
        $transactions = BarangKeluar::whereBetween('tanggal_transaksi', [$start, $end])
            ->get()
            ->groupBy(fn ($item) => Carbon::parse($item->tanggal_transaksi)->toDateString());

        // 2b. Transaksi pelunasan per hari (yang tanggal pelunasannya di range terpilih dan berbeda dengan tanggal transaksi)
        $pelunasans = BarangKeluar::whereBetween('tanggal_pelunasan', [$start, $end])
            ->whereColumn('tanggal_pelunasan', '!=', 'tanggal_transaksi')
            ->get()
            ->groupBy(fn ($item) => Carbon::parse($item->tanggal_pelunasan)->toDateString());

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
            $dayPelunasans    = $pelunasans->get($dateStr, collect());
            $dayExpenses     = $expenses->get($dateStr, collect());

            // HARGA = total omzet dari transaksi baru yang dibuat di hari ini
            $harga = $dayTransactions->sum('total_transaksi');

            // PENDAPATAN MASUK = Kas masuk dari transaksi baru + kas masuk dari pelunasan transaksi lama
            $incomeCreated = $dayTransactions->sum(function ($item) {
                $base = 0;
                // Jika langsung lunas pada hari pembuatan
                if ($item->status_pembayaran === 'lunas' && ($item->tanggal_pelunasan == $item->tanggal_transaksi || is_null($item->tanggal_pelunasan))) {
                    $base = $item->total_transaksi;
                } else {
                    // Jika DP (atau lunas di hari berbeda, saat pembuatan baru bayar DP)
                    $base = $item->dp_dibayar ?: 0;
                }
                // Tambahkan sisa uang kembalian BPJS ke pendapatan masuk
                return $base + ($item->sisa_bpjs ?: 0);
            });

            $incomePelunasan = $dayPelunasans->sum(function ($item) {
                // Sisa pembayaran (Total - DP)
                return $item->total_transaksi - ($item->dp_dibayar ?: 0);
            });

            $pendapatan = $incomeCreated + $incomePelunasan;

            // PENGELUARAN = detail per item dari Catat Pengeluaran
            $pengeluaranDetails = $dayExpenses->map(function ($exp) {
                $kategori = $exp->jenisPengeluaran?->nama ?? 'Pengeluaran';
                $label    = $exp->keterangan ? "{$kategori}: {$exp->keterangan}" : $kategori;
                return [
                    'label'   => $label,
                    'nominal' => (float) $exp->nominal,
                ];
            })->values()->toArray();

            // Tambahkan biaya pembelian lensa luar optik ke pengeluaran
            $totalBiayaBeliLensa = $dayTransactions->sum(fn ($item) => (float) ($item->biaya_beli_lensa ?? 0));
            if ($totalBiayaBeliLensa > 0) {
                $pengeluaranDetails[] = [
                    'label' => 'Pembelian Lensa (Luar Optik)',
                    'nominal' => $totalBiayaBeliLensa,
                ];
            }

            // Tambahkan biaya HPP aksesoris ke pengeluaran
            $totalBiayaAksesoris = $dayTransactions->sum(fn ($item) => (float) ($item->biaya_beli_aksesoris ?? 0));
            if ($totalBiayaAksesoris > 0) {
                $pengeluaranDetails[] = [
                    'label' => 'Modal HPP Aksesoris',
                    'nominal' => $totalBiayaAksesoris,
                ];
            }

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

        $filtered = array_filter($report, fn ($r) => $r['harga'] > 0 || $r['total_pengeluaran'] > 0 || $r['pendapatan'] > 0);
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

    public function printPembukuanHarian(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate   = $request->query('end_date');

        if (!$startDate || !$endDate) {
            abort(400, 'Tanggal mulai dan selesai harus diisi.');
        }

        $report = self::getPembukuanHarianData($startDate, $endDate);

        return view('reports.pembukuan-harian-print', [
            'report'          => $report['data'],
            'totals'          => $report['totals'],
            'startDate'       => Carbon::parse($startDate)->translatedFormat('d M Y'),
            'endDate'         => Carbon::parse($endDate)->translatedFormat('d M Y'),
            'cabang'          => session('cabang_nama', 'Suci Optikal'),
        ]);
    }

    /**
     * Hitung akumulasi saldo sebelum tanggal filter
     */
    private static function getRunningBalanceBeforeDate(Carbon $date, ?string $cabang): float
    {
        $cabangRecord = $cabang ? CabangOptik::where('nama', $cabang)->first() : null;
        $balance = $cabangRecord ? (float) $cabangRecord->saldo_awal : 0.0;

        $startOfDay = $date->copy()->startOfDay();
        
        $transactions = BarangKeluar::where('tanggal_transaksi', '<', $startOfDay)->get();
        $pelunasans = BarangKeluar::where('tanggal_pelunasan', '<', $startOfDay)
            ->whereColumn('tanggal_pelunasan', '!=', 'tanggal_transaksi')
            ->get();

        $expenseQuery = Pengeluaran::where('tanggal', '<', $date->toDateString());
        if ($cabang) {
            $expenseQuery->where('cabang', $cabang);
        }
        $expensesTotal = (float) $expenseQuery->sum('nominal');

        $incomeCreated = $transactions->sum(function ($item) {
            $base = 0;
            if ($item->status_pembayaran === 'lunas' && ($item->tanggal_pelunasan == $item->tanggal_transaksi || is_null($item->tanggal_pelunasan))) {
                $base = $item->total_transaksi;
            } else {
                $base = $item->dp_dibayar ?: 0;
            }
            return $base + ($item->sisa_bpjs ?: 0);
        });

        $incomePelunasan = $pelunasans->sum(function ($item) {
            return $item->total_transaksi - ($item->dp_dibayar ?: 0);
        });

        $pendapatan = $incomeCreated + $incomePelunasan;

        $biayaLensa = $transactions->sum(fn ($item) => (float) ($item->biaya_beli_lensa ?? 0));
        $biayaAksesoris = $transactions->sum(fn ($item) => (float) ($item->biaya_beli_aksesoris ?? 0));
        
        $totalPengeluaran = $expensesTotal + $biayaLensa + $biayaAksesoris;

        return $balance + $pendapatan - $totalPengeluaran;
    }

    /**
     * Kalkulasi data untuk Pembukuan Harian
     */
    public static function getPembukuanHarianData(string $startDate, string $endDate): array
    {
        $start  = Carbon::parse($startDate)->startOfDay();
        $end    = Carbon::parse($endDate)->endOfDay();
        $cabang = session('cabang_nama');

        $runningBalance = self::getRunningBalanceBeforeDate($start, $cabang);

        $transactions = BarangKeluar::with(['patient', 'frame', 'lens'])
            ->whereBetween('tanggal_transaksi', [$start, $end])
            ->get()
            ->groupBy(fn ($item) => Carbon::parse($item->tanggal_transaksi)->toDateString());

        $pelunasans = BarangKeluar::with(['patient', 'frame', 'lens'])
            ->whereBetween('tanggal_pelunasan', [$start, $end])
            ->whereColumn('tanggal_pelunasan', '!=', 'tanggal_transaksi')
            ->get()
            ->groupBy(fn ($item) => Carbon::parse($item->tanggal_pelunasan)->toDateString());

        $expenseQuery = Pengeluaran::with('jenisPengeluaran')
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()]);
        if ($cabang) {
            $expenseQuery->where('cabang', $cabang);
        }
        $expenses = $expenseQuery->orderBy('tanggal')->get()
            ->groupBy(fn ($item) => Carbon::parse($item->tanggal)->toDateString());

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
            $dayPelunasans   = $pelunasans->get($dateStr, collect());
            $dayExpenses     = $expenses->get($dateStr, collect());

            $mappedTransactions = [];
            foreach ($dayTransactions as $item) {
                $bpjsVal = '';
                if ($item->patient?->kategori === 'BPJS Kelas 1') {
                    $bpjsVal = 330000;
                } elseif ($item->patient?->kategori === 'BPJS Kelas 2') {
                    $bpjsVal = 220000;
                } elseif ($item->patient?->kategori === 'BPJS Kelas 3') {
                    $bpjsVal = 165000;
                }

                $mappedTransactions[] = [
                    'tanggal' => Carbon::parse($item->tanggal_transaksi)->translatedFormat('d M Y'),
                    'nama_pasien' => $item->patient?->nama ?? '-',
                    'bpjs' => $bpjsVal,
                    'no_bon' => $item->no_bon ?? $item->patient?->no_bon ?? '-',
                    'harga' => (float) $item->total_transaksi,
                    'dp' => (float) $item->dp_dibayar,
                    'sisa' => $item->status_pembayaran !== 'lunas' ? max(0, (float) $item->total_transaksi - (float) $item->dp_dibayar) : 0,
                    'frame' => $item->frame?->name ?? '-',
                    'lensa' => $item->lens?->name ?? '-',
                    'keterangan' => 'Transaksi Baru',
                ];
            }

            foreach ($dayPelunasans as $item) {
                $sisaPelunasan = $item->total_transaksi - ($item->dp_dibayar ?: 0);
                
                $bpjsVal = '';
                if ($item->patient?->kategori === 'BPJS Kelas 1') {
                    $bpjsVal = 330000;
                } elseif ($item->patient?->kategori === 'BPJS Kelas 2') {
                    $bpjsVal = 220000;
                } elseif ($item->patient?->kategori === 'BPJS Kelas 3') {
                    $bpjsVal = 165000;
                }

                $mappedTransactions[] = [
                    'tanggal' => Carbon::parse($item->tanggal_pelunasan)->translatedFormat('d M Y'),
                    'nama_pasien' => $item->patient?->nama ?? '-',
                    'bpjs' => $bpjsVal,
                    'no_bon' => $item->no_bon ?? $item->patient?->no_bon ?? '-',
                    'harga' => $sisaPelunasan, // Masuk ke harga karena ini pelunasan (uang masuk)
                    'dp' => 0,
                    'sisa' => 0, // Sudah lunas
                    'frame' => $item->frame?->name ?? '-',
                    'lensa' => $item->lens?->name ?? '-',
                    'keterangan' => 'Pelunasan Transaksi Tgl: ' . Carbon::parse($item->tanggal_transaksi)->translatedFormat('d/m/Y'),
                ];
            }

            // Pendapatan logic (re-use from DailyReportData)
            $incomeCreated = $dayTransactions->sum(function ($item) {
                $base = 0;
                if ($item->status_pembayaran === 'lunas' && ($item->tanggal_pelunasan == $item->tanggal_transaksi || is_null($item->tanggal_pelunasan))) {
                    $base = $item->total_transaksi;
                } else {
                    $base = $item->dp_dibayar ?: 0;
                }
                return $base + ($item->sisa_bpjs ?: 0);
            });

            $incomePelunasan = $dayPelunasans->sum(function ($item) {
                return $item->total_transaksi - ($item->dp_dibayar ?: 0);
            });

            $pendapatan = $incomeCreated + $incomePelunasan;

            // Pengeluaran details
            $pengeluaranDetails = $dayExpenses->map(function ($exp) {
                $kategori = $exp->jenisPengeluaran?->nama ?? 'Pengeluaran';
                $label    = $exp->keterangan ? "{$kategori}: {$exp->keterangan}" : $kategori;
                return [
                    'label'   => $label,
                    'nominal' => (float) $exp->nominal,
                ];
            })->values()->toArray();

            $totalBiayaBeliLensa = $dayTransactions->sum(fn ($item) => (float) ($item->biaya_beli_lensa ?? 0));
            if ($totalBiayaBeliLensa > 0) {
                $pengeluaranDetails[] = [
                    'label' => 'Pembelian Lensa (Luar Optik)',
                    'nominal' => $totalBiayaBeliLensa,
                ];
            }

            $totalBiayaAksesoris = $dayTransactions->sum(fn ($item) => (float) ($item->biaya_beli_aksesoris ?? 0));
            if ($totalBiayaAksesoris > 0) {
                $pengeluaranDetails[] = [
                    'label' => 'Modal HPP Aksesoris',
                    'nominal' => $totalBiayaAksesoris,
                ];
            }

            $totalPengeluaran = collect($pengeluaranDetails)->sum('nominal');

            $runningBalance += ($pendapatan - $totalPengeluaran);

            if (count($mappedTransactions) > 0 || $totalPengeluaran > 0 || $pendapatan > 0) {
                $report[] = [
                    'tanggal'             => Carbon::parse($dateStr)->translatedFormat('d M Y'),
                    'tanggal_raw'         => $dateStr,
                    'transactions'        => $mappedTransactions,
                    'total_pendapatan'    => (float) $pendapatan,
                    'pengeluaran_details' => $pengeluaranDetails,
                    'total_pengeluaran'   => (float) $totalPengeluaran,
                    'saldo_akhir'         => (float) $runningBalance,
                ];
            }
        }

        $filteredReport = array_values($report);

        $totals = [
            'pendapatan'  => collect($filteredReport)->sum('total_pendapatan'),
            'harga'       => collect($filteredReport)->sum('total_harga'),
            'dp'          => collect($filteredReport)->sum('total_dp'),
            'pengeluaran' => collect($filteredReport)->sum('total_pengeluaran'),
            'saldo'       => $runningBalance,
        ];

        return [
            'data'   => $filteredReport,
            'totals' => $totals,
        ];
    }
}
