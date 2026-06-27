<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Lensa Stok Optik - {{ $namaBulan }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, sans-serif;
            font-size: 9.5pt;
            color: #000;
            background: #fff;
            padding: 20px 24px;
        }

        /* ── Tables ── */
        .section-wrap {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px 8px;
            font-size: 9pt;
            vertical-align: middle;
        }
        th {
            background: #f2f2f2;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }
        .table-title {
            font-size: 10.5pt;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            padding: 6px;
            border: 1px solid #000;
            border-bottom: none;
            background: #e6e6e6;
        }
        .text-center { text-align: center; }
        .text-right  { text-align: right; }

        /* ── Print / no-print ── */
        .no-print {
            text-align: right;
            margin-bottom: 12px;
        }
        .btn-print {
            background: #000;
            color: #fff;
            padding: 7px 18px;
            border: none;
            cursor: pointer;
            font-weight: bold;
            font-size: 10pt;
        }

        @media print {
            body { padding: 8px 12px; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">&#128438; CETAK HALAMAN</button>
    </div>

    @forelse($groups as $categoryName => $transaksis)
        @php
            $totalJumlah = 0;
            $totalNominal = 0;
        @endphp
        <div class="section-wrap">
            <div class="table-title">LENSA {{ $categoryName }} {{ $tahun }}</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">NO</th>
                        <th style="width: 30%;">NAMA PASIEN</th>
                        <th style="width: 15%;">NO BON</th>
                        <th style="width: 25%;">LENSA</th>
                        <th style="width: 10%;">JUMLAH</th>
                        <th style="width: 15%;">NOMINAL</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transaksis as $i => $t)
                        @php
                            $pasang = $t->jumlah_lensa_pcs / 2;
                            $totalJumlah += $pasang;
                            $totalNominal += ($t->harga_lensa + ($t->biaya_faset ?? 0));
                        @endphp
                        <tr>
                            <td class="text-center">{{ $i + 1 }}</td>
                            <td>{{ strtoupper($t->patient?->nama ?? '-') }}</td>
                            <td class="text-center">{{ $t->no_bon }}</td>
                            <td>{{ strtoupper($t->lens?->name ?? '-') }}</td>
                            <td class="text-center">
                                {{ str_replace('.', ',', (float)$pasang) }}
                            </td>
                            <td class="text-right">
                                Rp{{ number_format($t->harga_lensa + ($t->biaya_faset ?? 0), 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="font-weight: bold; background: #f2f2f2;">
                        <td colspan="4" class="text-center">TOTAL</td>
                        <td class="text-center">{{ str_replace('.', ',', (float)$totalJumlah) }}</td>
                        <td class="text-right">Rp{{ number_format($totalNominal, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @empty
        <div style="text-align: center; margin-top: 50px; font-style: italic; color: #666;">
            Tidak ada data transaksi lensa untuk Stok Optik di bulan ini.
        </div>
    @endforelse

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => { window.print(); }, 500);
        });
    </script>
</body>
</html>
