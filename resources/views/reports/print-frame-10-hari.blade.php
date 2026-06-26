<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Frame Keluar 10 Hari ({{ $strTanggal }})</title>
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
            margin-bottom: 20px;
            position: relative;
        }
        .header h1 {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .fn-badge {
            margin-top: 8px;
            font-size: 9.5pt;
            font-weight: bold;
            text-align: left;
            display: inline-block;
            width: 100%;
            max-width: 600px;
            padding-left: 10px;
        }

        /* ── Tables ── */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
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

        /* ── Layout for Table 2 & Breakdown ── */
        .columns-wrap {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 40px;
            margin-top: 20px;
        }
        .col-left {
            flex: 1;
        }
        .col-right {
            width: 250px;
            font-size: 9pt;
            line-height: 1.5;
        }
        .nb-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .nb-category {
            font-weight: bold;
            margin-top: 10px;
            text-transform: uppercase;
        }
        .nb-item {
            padding-left: 10px;
            display: flex;
            justify-content: space-between;
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
            .columns-wrap {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">&#128438; CETAK HALAMAN</button>
    </div>

    {{-- ═══════════ HEADER ═══════════ --}}
    <div class="header">
        <h1>FRAME YANG KELUAR DARI TANGGAL {{ $strTanggal }}</h1>
        <div class="fn-badge">
            FN = {{ $totalPcs }} <br>
            &nbsp;&nbsp;&nbsp;&nbsp;↳ total
        </div>
    </div>

    {{-- ═══════════ TABEL 1: DAFTAR PASIEN & NO BON ═══════════ --}}
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">NO</th>
                <th style="width: 40%;">NAMA PASIEN</th>
                <th style="width: 20%;">NO BON</th>
                <th style="width: 35%;">FRAME</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tablePasien as $row)
                <tr>
                    <td class="text-center">{{ $row['no'] }}</td>
                    <td>{{ strtoupper($row['nama_pasien']) }}</td>
                    <td class="text-center">{{ $row['no_bon'] }}</td>
                    <td>{{ strtoupper($row['frame']) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center" style="color: #666; font-style: italic;">Tidak ada data transaksi.</td>
                </tr>
            @endforelse
        </tbody>
        @if($tablePasien->isNotEmpty())
            <tfoot>
                <tr style="font-weight: bold; background-color: #f2f2f2;">
                    <td colspan="3" class="text-center">TOTAL</td>
                    <td class="text-center" style="border-left: none;">{{ $totalPcs }}</td>
                </tr>
            </tfoot>
        @endif
    </table>

    {{-- ═══════════ TABEL 2 & BREAKDOWN KATEGORI ═══════════ --}}
    <div class="columns-wrap">
        {{-- Sisi Kiri: Tabel Ringkasan Frame --}}
        <div class="col-left">
            <table>
                <thead>
                    <tr>
                        <th style="width: 10%;">NO</th>
                        <th style="width: 60%;">FRAME</th>
                        <th style="width: 30%;">STOK KELUAR</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tableFrame as $row)
                        <tr>
                            <td class="text-center">{{ $row['no'] }}</td>
                            <td>{{ strtoupper($row['frame']) }}</td>
                            <td class="text-center">{{ $row['stok_keluar'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center" style="color: #666; font-style: italic;">Tidak ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if($tableFrame->isNotEmpty())
                    <tfoot>
                        <tr style="font-weight: bold; background-color: #f2f2f2;">
                            <td colspan="2" class="text-center">TOTAL</td>
                            <td class="text-center">{{ $totalPcs }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        {{-- Sisi Kanan: Rekap Pasien per Kategori & Gender --}}
        <div class="col-right">
            <div class="nb-title">NB :</div>
            @foreach($rekapPasien as $categoryName => $genders)
                <div class="nb-category">{{ $categoryName }} :</div>
                <div class="nb-item">
                    <span>PEREMPUAN</span>
                    <span>: {{ $genders['Perempuan'] ?: '-' }} PCS</span>
                </div>
                <div class="nb-item">
                    <span>LAKI-LAKI</span>
                    <span>: {{ $genders['Laki-laki'] ?: '-' }} PCS</span>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => { window.print(); }, 500);
        });
    </script>
</body>
</html>
