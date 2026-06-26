<?php

namespace App\Http\Controllers;

use App\Models\BarangKeluar;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LensKeluarPrintController extends Controller
{
    public function print(Request $request)
    {
        if (!auth()->check()) {
            abort(403, 'Unauthorized.');
        }

        $bulan = (int) $request->query('bulan', now()->month);
        $tahun = (int) $request->query('tahun', now()->year);
        $jenisKepemilikan = $request->query('jenis_kepemilikan', 'Stok Optik');

        // Ambil semua transaksi lensa di bulan & tahun terpilih dengan jenis kepemilikan tersebut
        $transaksis = BarangKeluar::with(['patient', 'lens.lensOwnershipCategory'])
            ->whereIn('tipe_transaksi', ['lensa', 'lengkap'])
            ->whereNotNull('lens_id')
            ->whereYear('tanggal_transaksi', $tahun)
            ->whereMonth('tanggal_transaksi', $bulan)
            ->whereHas('lens.lensOwnershipCategory', function ($query) use ($jenisKepemilikan) {
                $query->where('type', $jenisKepemilikan);
            })
            ->orderBy('tanggal_transaksi')
            ->orderBy('no_bon')
            ->get();

        $namaBulan = Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F Y');

        if ($jenisKepemilikan === 'Stok Optik') {
            // Kelompokkan per Kategori Kepemilikan (misal Domas, Poly, Essilor)
            $groups = $transaksis->groupBy(function ($t) {
                return $t->lens?->lensOwnershipCategory?->name ?? 'Lain-lain';
            });

            return view('reports.print-lensa-stok-optik', [
                'groups' => $groups,
                'namaBulan' => strtoupper($namaBulan),
                'tahun' => $tahun,
            ]);
        } else {
            // Luar Optik
            // Kelompokkan per Kategori Kepemilikan (misal Ayi, Hasbi)
            $rawGroups = $transaksis->groupBy(function ($t) {
                return $t->lens?->lensOwnershipCategory?->name ?? 'Lain-lain';
            });

            $groups = [];

            foreach ($rawGroups as $ownerName => $items) {
                // Cari semua nama lensa unik yang terjual oleh owner ini di bulan terpilih
                $lensNames = $items->map(fn($item) => $item->lens?->name ?? 'Lain-lain')
                    ->unique()
                    ->values()
                    ->toArray();

                // Kelompokkan data per tanggal
                $perTanggal = $items->groupBy(function ($item) {
                    return Carbon::parse($item->tanggal_transaksi)->format('Y-m-d');
                });

                // Proses data harian
                $processedDates = [];
                foreach ($perTanggal as $dateStr => $dayItems) {
                    $lensesQty = [];
                    foreach ($lensNames as $name) {
                        $qtyPcs = $dayItems->filter(fn($di) => ($di->lens?->name ?? 'Lain-lain') === $name)
                            ->sum('jumlah_lensa_pcs');
                        // Pasang = pcs / 2
                        $lensesQty[$name] = $qtyPcs / 2;
                    }

                    $totalPasang = $dayItems->sum('jumlah_lensa_pcs') / 2;
                    $nominal = $dayItems->sum('harga_lensa');

                    $processedDates[] = [
                        'tanggal_raw' => $dateStr,
                        'tanggal' => Carbon::parse($dateStr)->format('d/m/Y'),
                        'lenses_qty' => $lensesQty,
                        'total_pasang' => $totalPasang,
                        'nominal' => $nominal,
                    ];
                }

                // Urutkan berdasarkan tanggal
                usort($processedDates, fn($a, $b) => strcmp($a['tanggal_raw'], $b['tanggal_raw']));

                // Kelompokkan ke dalam dua periode (Tanggal 1-15 dan Tanggal 16-akhir)
                $period1 = [];
                $period2 = [];

                foreach ($processedDates as $row) {
                    $dayNum = (int) Carbon::parse($row['tanggal_raw'])->day;
                    if ($dayNum <= 15) {
                        $period1[] = $row;
                    } else {
                        $period2[] = $row;
                    }
                }

                $groups[$ownerName] = [
                    'lensNames' => $lensNames,
                    'period1' => $period1,
                    'period2' => $period2,
                    'total_pasang' => $items->sum('jumlah_lensa_pcs') / 2,
                    'nominal_total' => $items->sum('harga_lensa'),
                ];
            }

            return view('reports.print-lensa-luar-optik', [
                'groups' => $groups,
                'namaBulan' => strtoupper($namaBulan),
            ]);
        }
    }
}
