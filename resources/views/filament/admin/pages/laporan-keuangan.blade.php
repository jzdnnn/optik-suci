<x-filament-panels::page>
    <style>
        .report-card {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.02);
            border: 1px solid #e5e7eb;
            margin-bottom: 24px;
            overflow: hidden;
        }

        .dark .report-card {
            background-color: #18181b;
            border: 1px solid #27272a;
        }

        .filter-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
            padding: 24px;
        }

        @media (min-width: 768px) {
            .filter-container {
                flex-direction: row;
                align-items: flex-end;
                justify-content: space-between;
            }
        }

        .filter-form-wrapper { flex: 1; }

        .btn-print {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background-color: #f59e0b;
            color: #ffffff !important;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            text-decoration: none !important;
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(245, 158, 11, 0.2);
            height: 42px;
            border: none;
            cursor: pointer;
        }

        .btn-print:hover {
            background-color: #d97706;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(245, 158, 11, 0.3);
        }

        .btn-print svg { width: 18px; height: 18px; stroke-width: 2; }

        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 24px;
            border-bottom: 1px solid #f3f4f6;
            background-color: #fafafa;
        }

        .dark .report-header {
            border-bottom: 1px solid #27272a;
            background-color: #202023;
        }

        .report-title { font-size: 16px; font-weight: 700; color: #111827; }
        .dark .report-title { color: #f4f4f5; }

        .report-meta {
            font-size: 13px;
            font-weight: 500;
            color: #6b7280;
            background-color: #f3f4f6;
            padding: 6px 12px;
            border-radius: 6px;
        }

        .dark .report-meta { color: #a1a1aa; background-color: #27272a; }

        .table-responsive { overflow-x: auto; width: 100%; }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .report-table th {
            padding: 12px 16px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #4b5563;
            background-color: #f9fafb;
            border-bottom: 2px solid #e5e7eb;
            border-right: 1px solid #e5e7eb;
        }

        .dark .report-table th {
            color: #a1a1aa;
            background-color: #1e1e21;
            border-bottom: 2px solid #27272a;
            border-right: 1px solid #27272a;
        }

        .report-table td {
            padding: 10px 16px;
            font-size: 13px;
            color: #374151;
            border-bottom: 1px solid #f3f4f6;
            border-right: 1px solid #f3f4f6;
            vertical-align: top;
        }

        .dark .report-table td {
            color: #d4d4d8;
            border-bottom: 1px solid #27272a;
            border-right: 1px solid #27272a;
        }

        /* Baris awal setiap hari */
        .row-day-first td {
            border-top: 2px solid #e5e7eb !important;
        }

        .dark .row-day-first td {
            border-top: 2px solid #3f3f46 !important;
        }

        /* Baris subtotal pengeluaran */
        .row-subtotal td {
            background-color: #fef9f0;
            font-weight: 600;
        }

        .dark .row-subtotal td {
            background-color: #27200e;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .val-primary   { font-weight: 600; color: #111827; }
        .dark .val-primary { color: #f4f4f5; }
        .val-income    { font-weight: 600; color: #059669; }
        .val-expense   { color: #dc2626; }
        .val-expense-total { font-weight: 700; color: #dc2626; }
        .val-saldo     { font-weight: 700; color: #2563eb; }
        .val-desc      { color: #6b7280; font-size: 12px; }

        .report-table tfoot tr {
            background-color: #fafafa;
            border-top: 2px solid #e5e7eb;
        }

        .dark .report-table tfoot tr {
            background-color: #202023;
            border-top: 2px solid #27272a;
        }

        .report-table tfoot td {
            font-weight: 700;
            font-size: 14px;
            color: #111827;
            padding: 16px;
        }

        .dark .report-table tfoot td { color: #f4f4f5; }

        .no-data { padding: 48px 0; color: #6b7280; font-size: 14px; }

        .badge-no {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background-color: #f3f4f6;
            color: #374151;
            font-weight: 700;
            font-size: 12px;
        }

        .dark .badge-no {
            background-color: #27272a;
            color: #d4d4d8;
        }
    </style>

    <div class="space-y-6">
        {{-- Filter Form & Print Action --}}
        <div class="report-card filter-container">
            <div class="filter-form-wrapper">
                <form wire:submit.prevent>
                    {{ $this->form }}
                </form>
            </div>
            <div style="margin-top: 8px;">
                <a href="{{ $printUrl }}" target="_blank" class="btn-print">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak Laporan
                </a>
            </div>
        </div>

        {{-- Report Table --}}
        <div class="report-card">
            <div class="report-header">
                <h3 class="report-title">Rincian Laporan Keuangan Harian</h3>
                <span class="report-meta">
                    Saldo Awal: <strong class="val-primary">Rp {{ number_format($initialBalance, 0, ',', '.') }}</strong>
                </span>
            </div>

            <div class="table-responsive">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th rowspan="2" style="width:52px;" class="text-center">No</th>
                            <th rowspan="2" style="width:120px;">Tanggal</th>
                            <th rowspan="2" class="text-right" style="width:160px;">Harga</th>
                            <th rowspan="2" class="text-right" style="width:175px;">Pendapatan Masuk</th>
                            <th colspan="2" class="text-center" style="min-width:360px;">Pengeluaran</th>
                            <th rowspan="2" class="text-right" style="width:160px;">Saldo Akhir</th>
                        </tr>
                        <tr>
                            <th style="width:230px;">Keterangan</th>
                            <th class="text-right" style="width:150px;">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reportData as $index => $row)
                            @php
                                $details       = $row['pengeluaran_details'];
                                $rowCount      = max(count($details), 1);
                                $hasExpenses   = count($details) > 0;
                            @endphp

                            {{-- Baris pertama: tampilkan No, Tanggal, Harga, Pendapatan + pengeluaran item ke-1 --}}
                            <tr class="row-day-first">
                                <td class="text-center" rowspan="{{ $rowCount + ($hasExpenses ? 1 : 0) }}">
                                    <span class="badge-no">{{ $index + 1 }}</span>
                                </td>
                                <td class="val-primary" rowspan="{{ $rowCount + ($hasExpenses ? 1 : 0) }}">
                                    {{ $row['tanggal'] }}
                                </td>
                                <td class="text-right val-primary" rowspan="{{ $rowCount + ($hasExpenses ? 1 : 0) }}">
                                    Rp {{ number_format($row['harga'], 0, ',', '.') }}
                                </td>
                                <td class="text-right val-income" rowspan="{{ $rowCount + ($hasExpenses ? 1 : 0) }}">
                                    Rp {{ number_format($row['pendapatan'], 0, ',', '.') }}
                                </td>

                                @if($hasExpenses)
                                    <td class="val-desc">{{ $details[0]['label'] }}</td>
                                    <td class="text-right val-expense">Rp {{ number_format($details[0]['nominal'], 0, ',', '.') }}</td>
                                @else
                                    <td class="val-desc" style="color:#9ca3af; font-style:italic;">Tidak ada pengeluaran</td>
                                    <td class="text-right" style="color:#9ca3af;">-</td>
                                @endif

                                {{-- Saldo akhir hanya di baris pertama (dengan rowspan) --}}
                                @if(!$hasExpenses)
                                    <td class="text-right val-saldo">
                                        Rp {{ number_format($row['saldo'], 0, ',', '.') }}
                                    </td>
                                @endif
                            </tr>

                            {{-- Baris lanjutan pengeluaran (item ke-2 dst) --}}
                            @if($hasExpenses)
                                @foreach($details as $di => $detail)
                                    @if($di === 0) @continue @endif
                                    <tr>
                                        <td class="val-desc">{{ $detail['label'] }}</td>
                                        <td class="text-right val-expense">Rp {{ number_format($detail['nominal'], 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach

                                {{-- Baris subtotal + saldo akhir --}}
                                <tr class="row-subtotal">
                                    <td class="text-right" style="font-size:11px; color:#6b7280; padding-top:6px; padding-bottom:6px;">
                                        TOTAL
                                    </td>
                                    <td class="text-right val-expense-total">
                                        Rp {{ number_format($row['total_pengeluaran'], 0, ',', '.') }}
                                    </td>
                                    <td class="text-right val-saldo">
                                        Rp {{ number_format($row['saldo'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endif

                        @empty
                            <tr>
                                <td colspan="7" class="text-center no-data">
                                    Tidak ada data transaksi pada rentang tanggal terpilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2">TOTAL SUMMARY</td>
                            <td class="text-right">Rp {{ number_format($totals['harga'], 0, ',', '.') }}</td>
                            <td class="text-right val-income">Rp {{ number_format($totals['pendapatan'], 0, ',', '.') }}</td>
                            <td></td>
                            <td class="text-right val-expense-total">Rp {{ number_format($totals['pengeluaran'], 0, ',', '.') }}</td>
                            <td class="text-right val-saldo">Rp {{ number_format($totals['saldo'], 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
