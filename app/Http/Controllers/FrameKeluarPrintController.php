<?php

namespace App\Http\Controllers;

use App\Models\BarangKeluar;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class FrameKeluarPrintController extends Controller
{
    public function print(Request $request)
    {
        if (!auth()->check()) {
            abort(403, 'Unauthorized.');
        }

        $bulan = (int) $request->query('bulan', now()->month);
        $tahun = (int) $request->query('tahun', now()->year);

        // Ambil semua transaksi yang mengandung frame di bulan & tahun terpilih
        $transaksis = BarangKeluar::with(['patient', 'frame.frameCategory'])
            ->whereIn('tipe_transaksi', ['frame', 'lengkap'])
            ->whereNotNull('frame_id')
            ->whereYear('tanggal_transaksi', $tahun)
            ->whereMonth('tanggal_transaksi', $bulan)
            ->orderBy('no_bon')
            ->get();

        // Definisi grup kategori pasien (sesuai foto laporan)
        $grupKategori = [
            'BPJS Kelas 1' => [
                'label'     => 'FRAME BPJS KELAS 1',
                'kategori'  => ['BPJS Kelas 1'],
            ],
            'BPJS Kelas 2' => [
                'label'     => 'FRAME BPJS KELAS 2',
                'kategori'  => ['BPJS Kelas 2'],
            ],
            'BPJS Kelas 3' => [
                'label'     => 'FRAME BPJS KELAS 3',
                'kategori'  => ['BPJS Kelas 3'],
            ],
            'Umum' => [
                'label'     => 'FRAME UMUM',
                'kategori'  => ['Umum', 'Reguler', 'Swasta', 'Non-BPJS'],
            ],
            'Kacamata Baca' => [
                'label'     => 'KACAMATA BACA',
                'kategori'  => ['Kacamata Baca'],
            ],
        ];

        // Kelompokkan transaksi per kategori
        $groups = [];
        $sisaTransaksi = collect($transaksis); // untuk tangkap yang tidak masuk grup manapun ke Umum

        foreach ($grupKategori as $key => $def) {
            $rows = $transaksis->filter(function ($t) use ($def) {
                $kat = $t->patient?->kategori ?? '';
                return in_array($kat, $def['kategori']);
            })->values();

            $groups[$key] = [
                'label' => $def['label'],
                'rows'  => $rows,
            ];
        }

        // Tangkap pasien yang kategorinya tidak masuk ke grup manapun → masuk ke Umum
        $allMappedKategori = collect($grupKategori)->flatMap(fn ($d) => $d['kategori'])->toArray();
        $extra = $transaksis->filter(function ($t) use ($allMappedKategori) {
            $kat = $t->patient?->kategori ?? '';
            return !in_array($kat, $allMappedKategori);
        })->values();

        if ($extra->isNotEmpty()) {
            $groups['Umum']['rows'] = $groups['Umum']['rows']->merge($extra)->values();
        }

        // Hitung summary total
        $summary = [];
        $grandTotalPcs   = 0;
        $grandTotalHarga = 0;

        foreach ($groups as $key => $group) {
            $pcs   = $group['rows']->count();
            $harga = $group['rows']->sum('harga_frame');

            $summary[$key] = [
                'label' => $group['label'],
                'pcs'   => $pcs,
                'harga' => $harga,
            ];

            $grandTotalPcs   += $pcs;
            $grandTotalHarga += $harga;
        }

        $namaBulan = Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F Y');

        return view('reports.print-frame-bulanan', [
            'groups'          => $groups,
            'summary'         => $summary,
            'grandTotalPcs'   => $grandTotalPcs,
            'grandTotalHarga' => $grandTotalHarga,
            'bulan'           => $bulan,
            'tahun'           => $tahun,
            'namaBulan'       => strtoupper($namaBulan),
        ]);
    }

    public function printTenDays(Request $request)
    {
        if (!auth()->check()) {
            abort(403, 'Unauthorized.');
        }

        $tanggalAwal = $request->query('tanggal_awal', now()->format('Y-m-d'));
        $tanggalAkhir = $request->query('tanggal_akhir', now()->format('Y-m-d'));

        $start = Carbon::parse($tanggalAwal);
        $end = Carbon::parse($tanggalAkhir);

        // Ambil semua transaksi yang mengandung frame di range tanggal terpilih
        $transaksis = BarangKeluar::with(['patient', 'frame.frameCategory'])
            ->whereIn('tipe_transaksi', ['frame', 'lengkap'])
            ->whereNotNull('frame_id')
            ->whereBetween('tanggal_transaksi', [$tanggalAwal, $tanggalAkhir])
            ->orderBy('no_bon')
            ->get();

        // 1. Table 1: Frame keluar per pasien dan no bon
        $tablePasien = $transaksis->map(function ($t, $i) {
            return [
                'no' => $i + 1,
                'nama_pasien' => $t->patient?->nama ?? '-',
                'no_bon' => $t->no_bon,
                'frame' => $t->frame?->name ?? '-',
            ];
        });

        // 2. Table 2: Frame keluar per nama frame dan stok yang keluar
        $tableFrame = $transaksis->groupBy('frame_id')
            ->map(function ($group) {
                return [
                    'frame' => $group->first()->frame?->name ?? '-',
                    'stok_keluar' => $group->count(),
                ];
            })
            ->values()
            ->sortByDesc('stok_keluar')
            ->values()
            ->map(function ($item, $i) {
                $item['no'] = $i + 1;
                return $item;
            });

        // 3. Informasi data pasien (BPJS Kelas 1, 2, 3, dan Umum) by Gender
        $categories = [
            'BPJS Kelas 1' => 'BPJS KLS 1',
            'BPJS Kelas 2' => 'BPJS KLS 2',
            'BPJS Kelas 3' => 'BPJS KLS 3',
        ];

        $rekapPasien = [
            'BPJS KLS 1' => ['Laki-laki' => 0, 'Perempuan' => 0],
            'BPJS KLS 2' => ['Laki-laki' => 0, 'Perempuan' => 0],
            'BPJS KLS 3' => ['Laki-laki' => 0, 'Perempuan' => 0],
            'UMUM'       => ['Laki-laki' => 0, 'Perempuan' => 0],
        ];

        foreach ($transaksis as $t) {
            $patient = $t->patient;
            if (!$patient) {
                continue;
            }

            $kat = $patient->kategori;
            $jk = $patient->jenis_kelamin;

            // Map gender
            $genderKey = 'Perempuan'; // default fallback
            if ($jk && stripos($jk, 'laki') !== false) {
                $genderKey = 'Laki-laki';
            } elseif ($jk && stripos($jk, 'perempuan') !== false) {
                $genderKey = 'Perempuan';
            }

            // Map category
            $catKey = 'UMUM';
            if (isset($categories[$kat])) {
                $catKey = $categories[$kat];
            }

            $rekapPasien[$catKey][$genderKey]++;
        }

        // Format Date Range String
        $strTanggal = '';
        if ($start->year === $end->year) {
            if ($start->month === $end->month) {
                // Contoh: 1 S/D 10 APRIL 2026
                $strTanggal = $start->day . ' S/D ' . $end->day . ' ' . strtoupper($start->translatedFormat('F Y'));
            } else {
                // Contoh: 28 MARET S/D 6 APRIL 2026
                $strTanggal = $start->day . ' ' . strtoupper($start->translatedFormat('F')) . ' S/D ' . $end->day . ' ' . strtoupper($end->translatedFormat('F Y'));
            }
        } else {
            // Berbeda tahun
            $strTanggal = strtoupper($start->translatedFormat('d F Y')) . ' S/D ' . strtoupper($end->translatedFormat('d F Y'));
        }

        return view('reports.print-frame-10-hari', [
            'transaksis' => $transaksis,
            'tablePasien' => $tablePasien,
            'tableFrame' => $tableFrame,
            'rekapPasien' => $rekapPasien,
            'strTanggal' => $strTanggal,
            'totalPcs' => $transaksis->count(),
        ]);
    }
}
