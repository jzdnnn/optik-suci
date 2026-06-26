<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Lensa Luar Optik - {{ $namaBulan }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            color: #000;
            background: #fff;
            padding: 20px 24px;
        }

        /* ── Tables ── */
        .section-wrap {
            margin-bottom: 40px;
            page-break-inside: avoid;
        }
        .table-title {
            font-size: 10pt;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px 6px;
            font-size: 8.5pt;
            vertical-align: middle;
        }
        th {
            background: #f2f2f2;
            font-weight: bold;
            text-align: center;
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

    @forelse($groups as $ownerName => $data)
        @php
            $lensNames = $data['lensNames'];
            $colCount = count($lensNames);
            $totalCols = 5 + $colCount; // NO, Tanggal, [Lenses], Total Lensa, Nominal, Jumlah
            $rowNo = 1;
        @endphp
        <div class="section-wrap">
            <div class="table-title">LAPORAN LENSA FINISH {{ strtoupper($ownerName) }} {{ $namaBulan }}</div>
            <table>
                <thead>
                    <tr>
                        <th rowspan="2" style="width: 4%;">NO</th>
                        <th rowspan="2" style="width: 10%;">Tanggal</th>
                        <th colspan="{{ $colCount }}">Jumlah lensa (Pasang)</th>
                        <th rowspan="2" style="width: 10%;">Total Lensa (Pasang)</th>
                        <th rowspan="2" style="width: 12%;">Nominal (Rp)</th>
                        <th rowspan="2" style="width: 12%;">Jumlah (RP)</th>
                    </tr>
                    <tr>
                        @foreach($lensNames as $name)
                            <th>{{ $name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    {{-- ── PERIODE 1 (Tanggal 1-15) ── --}}
                    @php
                        $subtotalPasang1 = 0;
                        $subtotalNominal1 = 0;
                    @endphp
                    @foreach($data['period1'] as $row)
                        @php
                            $subtotalPasang1 += $row['total_pasang'];
                            $subtotalNominal1 += $row['nominal'];
                        @endphp
                        <tr>
                            <td class="text-center">{{ $rowNo++ }}</td>
                            <td class="text-center">{{ $row['tanggal'] }}</td>
                            @foreach($lensNames as $name)
                                <td class="text-center">
                                    {{ $row['lenses_qty'][$name] > 0 ? str_replace('.', ',', (float)$row['lenses_qty'][$name]) : '' }}
                                </td>
                            @endforeach
                            <td class="text-center">{{ str_replace('.', ',', (float)$row['total_pasang']) }}</td>
                            <td class="text-right">{{ number_format($row['nominal'], 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    @endforeach
                    @if(count($data['period1']) > 0)
                        {{-- Row Subtotal Periode 1 --}}
                        <tr style="background: #fafafa; font-weight: bold;">
                            <td colspan="{{ 2 + $colCount + 1 + 1 }}"></td>
                            <td class="text-right" style="border-top: 2px solid #000; border-bottom: 2px solid #000;">
                                {{ number_format($subtotalNominal1, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endif

                    {{-- Spacer Row if both periods have data --}}
                    @if(count($data['period1']) > 0 && count($data['period2']) > 0)
                        <tr>
                            <td colspan="{{ $totalCols }}" style="height: 10px; border: none; background: #fff;"></td>
                        </tr>
                    @endif

                    {{-- ── PERIODE 2 (Tanggal 16-Akhir) ── --}}
                    @php
                        $subtotalPasang2 = 0;
                        $subtotalNominal2 = 0;
                    @endphp
                    @foreach($data['period2'] as $row)
                        @php
                            $subtotalPasang2 += $row['total_pasang'];
                            $subtotalNominal2 += $row['nominal'];
                        @endphp
                        <tr>
                            <td class="text-center">{{ $rowNo++ }}</td>
                            <td class="text-center">{{ $row['tanggal'] }}</td>
                            @foreach($lensNames as $name)
                                <td class="text-center">
                                    {{ $row['lenses_qty'][$name] > 0 ? str_replace('.', ',', (float)$row['lenses_qty'][$name]) : '' }}
                                </td>
                            @endforeach
                            <td class="text-center">{{ str_replace('.', ',', (float)$row['total_pasang']) }}</td>
                            <td class="text-right">{{ number_format($row['nominal'], 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    @endforeach
                    @if(count($data['period2']) > 0)
                        {{-- Row Subtotal Periode 2 --}}
                        <tr style="background: #fafafa; font-weight: bold;">
                            <td colspan="{{ 2 + $colCount + 1 + 1 }}"></td>
                            <td class="text-right" style="border-top: 2px solid #000; border-bottom: 2px solid #000;">
                                {{ number_format($subtotalNominal2, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr style="font-weight: bold; background: #e6e6e6;">
                        <td colspan="2" class="text-center">TOTAL</td>
                        @foreach($lensNames as $name)
                            <td></td>
                        @endforeach
                        <td class="text-center">
                            {{ str_replace('.', ',', (float)$data['total_pasang']) }}
                        </td>
                        <td class="text-right">
                            {{ number_format($data['nominal_total'], 0, ',', '.') }}
                        </td>
                        <td class="text-right">
                            {{ number_format($data['nominal_total'], 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @empty
        <div style="text-align: center; margin-top: 50px; font-style: italic; color: #666;">
            Tidak ada data transaksi lensa untuk Luar Optik di bulan ini.
        </div>
    @endforelse

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => { window.print(); }, 500);
        });
    </script>
</body>
</html>
