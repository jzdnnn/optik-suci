<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Bulanan Keuangan - {{ $report->cabang }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            color: #000;
            background-color: #fff;
            margin: 0;
            padding: 20px;
            font-size: 10.5pt;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            text-transform: uppercase;
        }
        .header h1 {
            font-size: 14pt;
            margin: 0 0 5px 0;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .header h2 {
            font-size: 12pt;
            margin: 0;
            font-weight: bold;
        }
        
        .section-title {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 15px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        table.info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table.info-table td {
            padding: 4px 0;
            border: none;
        }
        .w-no { width: 5%; }
        .w-desc { width: 60%; }
        .w-val { width: 35%; text-align: right; }

        table.bordered-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table.bordered-table th, table.bordered-table td {
            border: 1px solid #000;
            padding: 5px 8px;
            font-size: 9.5pt;
        }
        table.bordered-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: left;
        }

        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .line-double {
            border-bottom: 3px double #000;
            margin: 15px 0;
        }
        .line-single {
            border-bottom: 1px solid #000;
            margin: 10px 0;
        }

        .footer-note {
            margin-top: 30px;
            font-size: 9pt;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .signature-box {
            text-align: center;
            width: 200px;
        }
        .signature-space {
            height: 50px;
        }
        @media print {
            body {
                padding: 0;
                margin: 0;
            }
            .no-print {
                display: none;
            }
        }
        .btn-print {
            background-color: #000;
            color: #fff;
            padding: 8px 16px;
            border: none;
            cursor: pointer;
            font-weight: bold;
            font-family: inherit;
            margin-bottom: 20px;
            display: inline-block;
        }
        
        .flex-container {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }
        .flex-child {
            flex: 1;
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: right;">
        <button class="btn-print" onclick="window.print()">CETAK HALAMAN</button>
    </div>

    <div class="header">
        <h1>LAPORAN BULANAN BULAN {{ strtoupper($report->nama_bulan) }} {{ $report->tahun }}</h1>
        <h2>{{ strtoupper($report->cabang) }}</h2>
    </div>

    <!-- PENDAPATAN & SETORAN MINGGUAN -->
    <div class="flex-container">
        <!-- Section A: Pendapatan -->
        <div class="flex-child">
            <div class="section-title">A. PENDAPATAN</div>
            <table class="info-table">
                <tr>
                    <td class="w-no">1</td>
                    <td class="w-desc">OMZET</td>
                    <td class="w-val">Rp {{ number_format($report->calculated_omzet, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="w-no">2</td>
                    <td class="w-desc">PENDAPATAN BPJS</td>
                    <td class="w-val">Rp {{ number_format($report->pendapatan_bpjs, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="w-no">3</td>
                    <td class="w-desc">PENDAPATAN HARIAN</td>
                    <td class="w-val">Rp {{ number_format($report->calculated_pendapatan_harian, 0, ',', '.') }}</td>
                </tr>
                <tr style="font-weight: bold; border-top: 1px solid #000;">
                    <td></td>
                    <td>JUMLAH PENDAPATAN</td>
                    <td class="w-val" style="border-top: 1px solid #000;">Rp {{ number_format($report->total_pendapatan, 0, ',', '.') }}</td>
                </tr>
                <tr style="font-weight: bold;">
                    <td></td>
                    <td>SELISIH</td>
                    <td class="w-val">Rp {{ number_format($report->total_selisih, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <!-- Section C: Setoran Mingguan -->
        <div class="flex-child">
            <div class="section-title">C. SETORAN MINGGUAN</div>
            <table class="info-table">
                <tr>
                    <td class="w-no">1</td>
                    <td class="w-desc">SETORAN I CASH</td>
                    <td class="w-val">Rp {{ number_format($report->setoran_minggu_1, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="w-no">2</td>
                    <td class="w-desc">SETORAN II CASH</td>
                    <td class="w-val">Rp {{ number_format($report->setoran_minggu_2, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="w-no">3</td>
                    <td class="w-desc">SETORAN III CASH</td>
                    <td class="w-val">Rp {{ number_format($report->setoran_minggu_3, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="w-no">4</td>
                    <td class="w-desc">SETORAN IV CASH</td>
                    <td class="w-val">Rp {{ number_format($report->setoran_minggu_4, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="w-no">5</td>
                    <td class="w-desc">SETORAN V CASH</td>
                    <td class="w-val">Rp {{ number_format($report->setoran_minggu_5, 0, ',', '.') }}</td>
                </tr>
                <tr style="font-weight: bold; border-top: 1px solid #000;">
                    <td></td>
                    <td>TOTAL SETORAN</td>
                    <td class="w-val" style="border-top: 1px solid #000;">Rp {{ number_format($report->total_setoran_bulanan, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Section B: Rincian Selisih -->
    <div class="section-title">B. RINCIAN SELISIH</div>
    <table class="bordered-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 20%;">No. Bon</th>
                <th style="width: 25%;">Nama Pegawai</th>
                <th style="width: 30%;">Keterangan</th>
                <th style="width: 20%;" class="text-right">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($report->selisih_details ?? [] as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item['no_bon'] }}</td>
                    <td>{{ $item['nama_pegawai'] }}</td>
                    <td>{{ $item['keterangan'] }}</td>
                    <td class="text-right">Rp {{ number_format((float)($item['nominal'] ?? 0), 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="color: #666;">Tidak ada data selisih.</td>
                </tr>
            @endforelse
            <tr style="font-weight: bold; background-color: #fafafa;">
                <td colspan="4" class="text-right">TOTAL SELURUH SELISIH:</td>
                <td class="text-right">Rp {{ number_format($report->total_selisih, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="line-single"></div>

    <!-- PENGELUARAN SECTIONS -->
    <div class="section-title">D. PENGELUARAN</div>
    
    <div class="flex-container">
        <!-- Column 1: Harian (Operasional) -->
        <div class="flex-child">
            <div style="font-weight: bold; margin-bottom: 5px;">A. Harian (Operasional):</div>
            <table class="info-table">
                @php $idx = 1; @endphp
                @forelse($report->getExpensesByType('operasional') as $expense)
                    <tr>
                        <td class="w-no">{{ $idx++ }}.</td>
                        <td class="w-desc">{{ $expense->nama }} {!! $expense->keterangan_list ? '<span style="font-size: 8.5pt; color: #555;">('.e($expense->keterangan_list).')</span>' : '' !!}</td>
                        <td class="w-val">Rp {{ number_format($expense->total_nominal, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="color: #666; font-style: italic;">Tidak ada data pengeluaran operasional.</td>
                    </tr>
                @endforelse
                <tr style="font-weight: bold; border-top: 1px solid #000;">
                    <td></td>
                    <td>JUMLAH A</td>
                    <td class="w-val" style="border-top: 1px solid #000;">Rp {{ number_format($report->total_pengeluaran_operasional, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <!-- Column 2: Stok & Gaji -->
        <div class="flex-child">
            <!-- B. Lensa Gosok, Stok, Frame & Lap -->
            <div style="font-weight: bold; margin-bottom: 5px;">B. Lensa Gosok, Stok, Frame & Lap:</div>
            <table class="info-table">
                @php $idx = 1; @endphp
                @forelse($report->getExpensesByType('stok') as $expense)
                    <tr>
                        <td class="w-no">{{ $idx++ }}.</td>
                        <td class="w-desc">{{ $expense->nama }} {!! $expense->keterangan_list ? '<span style="font-size: 8.5pt; color: #555;">('.e($expense->keterangan_list).')</span>' : '' !!}</td>
                        <td class="w-val">Rp {{ number_format($expense->total_nominal, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="color: #666; font-style: italic;">Tidak ada data pengeluaran stok.</td>
                    </tr>
                @endforelse
                <tr style="font-weight: bold; border-top: 1px solid #000;">
                    <td></td>
                    <td>JUMLAH B</td>
                    <td class="w-val" style="border-top: 1px solid #000;">Rp {{ number_format($report->total_pengeluaran_stok, 0, ',', '.') }}</td>
                </tr>
            </table>

            <!-- D. Pengeluaran Gaji, DLL -->
            <div style="font-weight: bold; margin-top: 15px; margin-bottom: 5px;">D. Pengeluaran Gaji, DLL:</div>
            <table class="info-table">
                @php $idx = 1; @endphp
                @forelse($report->getExpensesByType('gaji') as $expense)
                    <tr>
                        <td class="w-no">{{ $idx++ }}.</td>
                        <td class="w-desc">{{ $expense->nama }} {!! $expense->keterangan_list ? '<span style="font-size: 8.5pt; color: #555;">('.e($expense->keterangan_list).')</span>' : '' !!}</td>
                        <td class="w-val">Rp {{ number_format($expense->total_nominal, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="color: #666; font-style: italic;">Tidak ada data pengeluaran gaji.</td>
                    </tr>
                @endforelse
                <tr style="font-weight: bold; border-top: 1px solid #000;">
                    <td></td>
                    <td>JUMLAH D</td>
                    <td class="w-val" style="border-top: 1px solid #000;">Rp {{ number_format($report->total_pengeluaran_gaji, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="line-single"></div>

    <!-- C. Total Seluruh Pengeluaran -->
    <table class="info-table" style="font-weight: bold; font-size: 11pt;">
        <tr>
            <td style="width: 65%;">C. TOTAL SELURUH PENGELUARAN (A + B + D)</td>
            <td style="width: 35%; text-align: right;">Rp {{ number_format($report->total_seluruh_pengeluaran, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="line-double"></div>

    <!-- REKAPITULASI AKHIR -->
    <div class="section-title">REKAPITULASI AKHIR</div>
    <table class="info-table" style="font-weight: bold; font-size: 11pt;">
        <tr>
            <td style="width: 65%;">TOTAL PENDAPATAN (A)</td>
            <td style="width: 35%; text-align: right;">Rp {{ number_format($report->total_pendapatan, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>TOTAL PENGELUARAN (C)</td>
            <td style="text-align: right; color: #ef4444;">- Rp {{ number_format($report->total_seluruh_pengeluaran, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>TOTAL SELISIH (B)</td>
            <td style="text-align: right; color: #ef4444;">- Rp {{ number_format($report->total_selisih, 0, ',', '.') }}</td>
        </tr>
        <tr style="font-size: 12pt; border-top: 2px solid #000;">
            <td style="border-top: 2px solid #000; padding-top: 8px;">LABA BERSIH</td>
            <td style="text-align: right; border-top: 2px solid #000; padding-top: 8px; color: #3b82f6;">Rp {{ number_format($report->laba_bersih, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="footer-note">
        <div>
            Dicetak pada: {{ \Illuminate\Support\Carbon::now()->translatedFormat('d F Y H:i') }}
        </div>
        <div class="signature-box">
            <p>Penanggung Jawab,</p>
            <div class="signature-space"></div>
            <p>(.........................)</p>
        </div>
    </div>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
