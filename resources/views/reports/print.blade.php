<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan Harian - {{ $cabang ?? 'Suci Optikal' }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 20px;
            font-size: 10pt;
        }
        .header {
            text-align: center;
            margin-bottom: 18px;
            text-transform: uppercase;
        }
        .header h1 { font-size: 14pt; margin: 0 0 3px 0; font-weight: bold; }
        .header h2 { font-size: 12pt; margin: 0 0 2px 0; font-weight: bold; }
        .header p  { margin: 0; font-size: 10pt; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px 7px;
            font-size: 9pt;
            vertical-align: top;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }
        .text-right  { text-align: right; }
        .text-center { text-align: center; }

        /* Baris pengeluaran tanpa border kiri pada No/Tanggal/Harga/Pendapatan */
        .td-empty { border-top: none; border-bottom: none; }
        .td-first-day { border-top: 2px solid #000; }

        /* Baris subtotal pengeluaran */
        .row-subtotal td { font-weight: bold; background-color: #f9f9f9; }

        .footer-note {
            margin-top: 30px;
            font-size: 9pt;
            display: flex;
            justify-content: space-between;
        }
        .signature-box { text-align: center; width: 180px; }
        .signature-space { height: 55px; }

        @media print {
            body { padding: 8px; margin: 0; }
            .no-print { display: none !important; }
        }
        .btn-print {
            background: #000;
            color: #fff;
            padding: 8px 18px;
            border: none;
            cursor: pointer;
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 16px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align:right; margin-bottom:8px;">
        <button class="btn-print" onclick="window.print()">&#128438; CETAK HALAMAN</button>
    </div>

    <div class="header">
        <h1>Laporan Keuangan Harian</h1>
        <h2>{{ $cabang ?? 'Suci Optikal' }}</h2>
        <p>Periode: {{ $startDate }} s/d {{ $endDate }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width:4%;">No</th>
                <th rowspan="2" style="width:10%;">Tanggal</th>
                <th rowspan="2" style="width:14%;">Harga</th>
                <th rowspan="2" style="width:15%;">Pendapatan Masuk</th>
                <th colspan="2" style="width:42%;">Pengeluaran</th>
                <th rowspan="2" style="width:15%;">Saldo Akhir</th>
            </tr>
            <tr>
                <th style="width:27%;">Keterangan</th>
                <th style="width:15%;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            {{-- Saldo Awal --}}
            <tr>
                <td class="text-center">-</td>
                <td><strong>Saldo Awal</strong></td>
                <td class="text-right">-</td>
                <td class="text-right">-</td>
                <td colspan="2" style="color:#555; font-size:8.5pt;">Saldo sebelum periode dimulai</td>
                <td class="text-right"><strong>Rp {{ number_format($initial_balance, 0, ',', '.') }}</strong></td>
            </tr>

            @php $no = 1; @endphp
            @foreach($report as $row)
                @php
                    $details     = $row['pengeluaran_details'];
                    $hasExpenses = count($details) > 0;
                    $rowspan     = $hasExpenses ? (count($details) + 1) : 1;
                @endphp

                {{-- Baris pertama (No, Tanggal, Harga, Pendapatan masuk + pengeluaran item ke-1) --}}
                <tr>
                    <td class="text-center" rowspan="{{ $rowspan }}">{{ $no++ }}</td>
                    <td rowspan="{{ $rowspan }}">{{ $row['tanggal'] }}</td>
                    <td class="text-right" rowspan="{{ $rowspan }}">Rp {{ number_format($row['harga'], 0, ',', '.') }}</td>
                    <td class="text-right" rowspan="{{ $rowspan }}">Rp {{ number_format($row['pendapatan'], 0, ',', '.') }}</td>

                    @if($hasExpenses)
                        <td>{{ $details[0]['label'] }}</td>
                        <td class="text-right">Rp {{ number_format($details[0]['nominal'], 0, ',', '.') }}</td>
                    @else
                        <td style="color:#999; font-style:italic;">Tidak ada pengeluaran</td>
                        <td class="text-right">-</td>
                        <td class="text-right">Rp {{ number_format($row['saldo'], 0, ',', '.') }}</td>
                    @endif
                </tr>

                @if($hasExpenses)
                    {{-- Baris lanjutan pengeluaran ke-2 dst --}}
                    @foreach($details as $di => $detail)
                        @if($di === 0) @continue @endif
                        <tr>
                            <td>{{ $detail['label'] }}</td>
                            <td class="text-right">Rp {{ number_format($detail['nominal'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach

                    {{-- Baris subtotal + saldo akhir --}}
                    <tr class="row-subtotal">
                        <td class="text-right" style="font-size:8pt;">Total Pengeluaran</td>
                        <td class="text-right">Rp {{ number_format($row['total_pengeluaran'], 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($row['saldo'], 0, ',', '.') }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
        <tfoot>
            <tr style="font-weight:bold; background:#f0f0f0;">
                <td colspan="2" class="text-center">TOTAL</td>
                <td class="text-right">Rp {{ number_format($totals['harga'], 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totals['pendapatan'], 0, ',', '.') }}</td>
                <td></td>
                <td class="text-right">Rp {{ number_format($totals['pengeluaran'], 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totals['saldo'], 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer-note">
        <div>Dicetak pada: {{ \Illuminate\Support\Carbon::now()->translatedFormat('d F Y H:i') }}</div>
        <div class="signature-box">
            <p>Penanggung Jawab,</p>
            <div class="signature-space"></div>
            <p>(.........................)</p>
        </div>
    </div>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => { window.print(); }, 500);
        });
    </script>
</body>
</html>
