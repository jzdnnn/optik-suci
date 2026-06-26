<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Frame Keluar Bulan {{ $namaBulan }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, sans-serif;
            font-size: 9.5pt;
            color: #000;
            background: #fff;
            padding: 20px 24px;
        }

        /* ── Header ── */
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .header h1 {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header h2 {
            font-size: 10pt;
            font-weight: normal;
            margin-top: 2px;
        }

        /* ── Tables ── */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }
        th, td {
            border: 1px solid #000;
            padding: 3px 6px;
            font-size: 9pt;
            vertical-align: middle;
        }
        th {
            background: #e8e8e8;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }
        .text-center { text-align: center; }
        .text-right  { text-align: right; }

        /* ── Section heading ── */
        .section-wrap {
            margin-bottom: 14px;
        }
        .section-header {
            font-weight: bold;
            font-size: 9pt;
        }

        /* ── Total row ── */
        .row-total td {
            font-weight: bold;
            background: #f5f5f5;
        }

        /* ── Summary table ── */
        .summary-wrap {
            margin-top: 18px;
        }
        .summary-title {
            font-weight: bold;
            font-size: 9.5pt;
            text-align: center;
            padding: 5px;
            background: #d0d0d0;
            border: 1px solid #000;
            text-transform: uppercase;
            border-bottom: none;
        }
        .summary-wrap table th {
            background: #e8e8e8;
        }

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

    {{-- ═══════════ HEADER ═══════════ --}}
    <div class="header">
        <h1>Frame Yang Keluar Bulan {{ $namaBulan }}</h1>
        @php
            $parts = [];
            foreach($summary as $s) {
                if($s['pcs'] > 0) $parts[] = $s['pcs'];
            }
        @endphp
        <h2>FN: {{ implode(' + ', array_filter($parts)) }} = {{ $grandTotalPcs }} pcs</h2>
    </div>

    {{-- ═══════════ TABEL PER KATEGORI ═══════════ --}}
    @foreach($groups as $key => $group)
        @php $rows = $group['rows']; @endphp
        <div class="section-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="width:4%;">NO</th>
                        <th style="width:25%;">NAMA PASIEN</th>
                        <th style="width:10%;">NO BON</th>
                        <th style="width:35%;">{{ $group['label'] }}</th>
                        <th style="width:26%;" colspan="2">HARGA</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $i => $t)
                        <tr>
                            <td class="text-center">{{ $i + 1 }}</td>
                            <td>{{ $t->patient?->nama ?? '-' }}</td>
                            <td class="text-center">{{ $t->no_bon }}</td>
                            <td>{{ $t->frame?->name ?? '-' }}</td>
                            <td style="width:6%;">Rp</td>
                            <td class="text-right" style="width:20%;">
                                {{ number_format($t->harga_frame, 0, ',', '.') }},00
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center" style="color:#888; font-style:italic;">
                                Tidak ada data
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="row-total">
                        <td colspan="3" class="text-center">TOTAL</td>
                        <td class="text-center">{{ $rows->count() }} PCS</td>
                        <td>Rp</td>
                        <td class="text-right">
                            {{ number_format($rows->sum('harga_frame'), 0, ',', '.') }},00
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endforeach

    {{-- ═══════════ RINGKASAN TOTAL PENGELUARAN ═══════════ --}}
    <div class="summary-wrap">
        <div class="summary-title">Total Pengeluaran Frame</div>
        <table>
            <thead>
                <tr>
                    <th style="width:5%;">NO</th>
                    <th style="width:45%;">KETERANGAN</th>
                    <th style="width:15%;">TOTAL</th>
                    <th style="width:35%;" colspan="2">HARGA</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach($summary as $key => $s)
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td>{{ $s['label'] }}</td>
                        <td class="text-center">{{ $s['pcs'] }}</td>
                        <td style="width:6%;">Rp</td>
                        <td class="text-right" style="width:29%;">
                            {{ number_format($s['harga'], 0, ',', '.') }},00
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="row-total">
                    <td colspan="2" class="text-center">TOTAL</td>
                    <td class="text-center">{{ $grandTotalPcs }}</td>
                    <td>Rp</td>
                    <td class="text-right">{{ number_format($grandTotalHarga, 0, ',', '.') }},00</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => { window.print(); }, 500);
        });
    </script>
</body>
</html>
