<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pembukuan Harian - {{ $cabang ?? 'Suci Optikal' }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; color: #000; background: #fff; margin: 0; padding: 20px; font-size: 9pt; }
        .header { text-align: center; margin-bottom: 18px; text-transform: uppercase; }
        .header h1 { font-size: 14pt; margin: 0 0 3px 0; font-weight: bold; }
        .header h2 { font-size: 12pt; margin: 0 0 2px 0; font-weight: bold; }
        .header p  { margin: 0; font-size: 10pt; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th, td { border: 1px solid #000; padding: 4px 6px; font-size: 8pt; vertical-align: top; }
        th { background-color: #f0f0f0; font-weight: bold; text-align: center; text-transform: uppercase; }
        
        .text-right  { text-align: right; }
        .text-center { text-align: center; }

        .row-subtotal td { font-weight: bold; background-color: #f9f9f9; }
        .no-data { padding: 20px 0; font-style: italic; color: #666; }

        .footer-note { margin-top: 30px; font-size: 9pt; display: flex; justify-content: space-between; }
        .signature-box { text-align: center; width: 180px; }
        .signature-space { height: 55px; }

        @media print { body { padding: 8px; margin: 0; } .no-print { display: none !important; } }
        .btn-print { background: #000; color: #fff; padding: 8px 18px; border: none; cursor: pointer; font-weight: bold; font-size: 11pt; margin-bottom: 16px; display: inline-block; }
    </style>
</head>
<body>
    <div class="no-print" style="text-align:right; margin-bottom:8px;">
        <button class="btn-print" onclick="window.print()">&#128438; CETAK HALAMAN</button>
    </div>

    <div class="header">
        <h1>Pembukuan Harian</h1>
        <h2>{{ $cabang ?? 'Suci Optikal' }}</h2>
        <p>Periode: {{ $startDate }} s/d {{ $endDate }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:7%;">Tanggal</th>
                <th style="width:13%;">Nama Pasien</th>
                <th style="width:9%;">BPJS</th>
                <th style="width:7%;">No Bon</th>
                <th style="width:10%;">Harga</th>
                <th style="width:9%;">DP</th>
                <th style="width:9%;">Sisa</th>
                <th style="width:16%;">Pengeluaran</th>
                <th style="width:10%;">Saldo Akhir</th>
                <th style="width:5%;">Frame</th>
                <th style="width:5%;">Lensa</th>
            </tr>
        </thead>
        <tbody>
            @forelse($report as $row)
                @if(count($row['transactions']) === 0)
                    <tr>
                        <td>{{ $row['tanggal'] }}</td>
                        <td colspan="5" class="text-center" style="color: #666; font-style: italic;">Tidak ada transaksi pasien</td>
                        <td class="text-center">-</td>
                        <td class="text-center">-</td>
                        <td class="text-center">-</td>
                        <td class="text-center">-</td>
                        <td class="text-center">-</td>
                    </tr>
                @else
                    @foreach($row['transactions'] as $trx)
                        <tr>
                            <td>{{ $trx['tanggal'] }}</td>
                            <td>
                                <strong>{{ $trx['nama_pasien'] }}</strong>
                                @if($trx['keterangan'] !== 'Transaksi Baru')
                                    <br><small style="color: #555;">{{ $trx['keterangan'] }}</small>
                                @endif
                            </td>
                            <td class="text-right">{{ $trx['bpjs'] ? number_format($trx['bpjs'], 0, ',', '.') : '-' }}</td>
                            <td>{{ $trx['no_bon'] }}</td>
                            <td class="text-right">{{ number_format($trx['harga'], 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($trx['dp'], 0, ',', '.') }}</td>
                            <td class="text-right">{{ $trx['sisa'] > 0 ? number_format($trx['sisa'], 0, ',', '.') : '-' }}</td>
                            <td class="text-center">-</td>
                            <td class="text-center">-</td>
                            <td>{{ $trx['frame'] }}</td>
                            <td>{{ $trx['lensa'] }}</td>
                        </tr>
                    @endforeach
                @endif

                <tr class="row-subtotal">
                    <td colspan="4" class="text-right">TOTAL</td>
                    <td class="text-right">{{ number_format($row['total_harga'] ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($row['total_dp'] ?? 0, 0, ',', '.') }}</td>
                    <td></td>
                    <td>
                        @forelse($row['pengeluaran_details'] as $detail)
                            <div style="display:flex; justify-content:space-between; margin-bottom:2px; font-weight:normal;">
                                <span>{{ $detail['label'] }}</span>
                                <span>{{ number_format($detail['nominal'], 0, ',', '.') }}</span>
                            </div>
                        @empty
                            <div style="font-weight:normal; font-style:italic; color:#666;">-</div>
                        @endforelse
                        <div style="border-top:1px solid #ccc; margin-top:4px; padding-top:2px; display:flex; justify-content:space-between;">
                            <span>TOTAL</span>
                            <span>{{ number_format($row['total_pengeluaran'], 0, ',', '.') }}</span>
                        </div>
                    </td>
                    <td class="text-right">{{ number_format($row['saldo_akhir'], 0, ',', '.') }}</td>
                    <td colspan="2"></td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center no-data">Tidak ada data transaksi atau pengeluaran.</td>
                </tr>
            @endforelse
        </tbody>
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
        window.addEventListener('DOMContentLoaded', () => { setTimeout(() => { window.print(); }, 500); });
    </script>
</body>
</html>
