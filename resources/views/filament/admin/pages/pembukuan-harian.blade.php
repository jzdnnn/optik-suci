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
            vertical-align: middle;
        }

        .dark .report-table td {
            color: #d4d4d8;
            border-bottom: 1px solid #27272a;
            border-right: 1px solid #27272a;
        }

        .row-subtotal td {
            background-color: #fef9f0;
            font-weight: 600;
            border-top: 2px solid #fcd34d !important;
        }

        .dark .row-subtotal td {
            background-color: #27200e;
            border-top: 2px solid #b45309 !important;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .val-primary   { font-weight: 600; color: #111827; }
        .dark .val-primary { color: #f4f4f5; }
        .val-income    { font-weight: 600; color: #059669; }
        .val-expense   { color: #dc2626; }
        .val-saldo     { font-weight: 700; color: #2563eb; }

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
    </style>

    <div class="space-y-6">
        {{-- Filter Form --}}
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
                <h3 class="report-title">Tabel Pembukuan Harian</h3>
            </div>

            <div class="table-responsive">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th style="width:100px;">Tanggal</th>
                            <th style="width:150px;">Nama Pasien</th>
                            <th class="text-right" style="width:120px;">BPJS</th>
                            <th style="width:100px;">No Bon</th>
                            <th class="text-right" style="width:130px;">Pendapatan/Harga</th>
                            <th class="text-right" style="width:100px;">DP</th>
                            <th class="text-right" style="width:110px;">Sisa Pembayaran</th>
                            <th style="width:220px;">Pengeluaran</th>
                            <th class="text-right" style="width:140px;">Saldo Akhir</th>
                            <th style="width:120px;">Frame</th>
                            <th style="width:120px;">Lensa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reportData as $row)
                            {{-- Transaction Rows --}}
                            @if(count($row['transactions']) === 0)
                                <tr>
                                    <td>{{ $row['tanggal'] }}</td>
                                    <td colspan="5" class="text-center" style="color: #9ca3af; font-style: italic; font-size: 12px;">Tidak ada transaksi pasien</td>
                                    <td class="text-center" style="background-color: rgba(0,0,0,0.02);">-</td>
                                    <td class="text-center" style="background-color: rgba(0,0,0,0.02);">-</td>
                                    <td class="text-center" style="color: #9ca3af;">-</td>
                                    <td class="text-center" style="color: #9ca3af;">-</td>
                                    <td class="text-center" style="color: #9ca3af;">-</td>
                                </tr>
                            @else
                                @foreach($row['transactions'] as $trx)
                                    <tr>
                                        <td>{{ $trx['tanggal'] }}</td>
                                        <td>
                                            <span class="font-medium">{{ $trx['nama_pasien'] }}</span>
                                            @if($trx['keterangan'] !== 'Transaksi Baru')
                                                <br><small style="color: #6b7280; font-size: 11px;">{{ $trx['keterangan'] }}</small>
                                            @endif
                                        </td>
                                        <td class="text-right">{{ $trx['bpjs'] ? 'Rp ' . number_format($trx['bpjs'], 0, ',', '.') : '-' }}</td>
                                        <td>{{ $trx['no_bon'] }}</td>
                                        <td class="text-right val-primary">Rp {{ number_format($trx['harga'], 0, ',', '.') }}</td>
                                        <td class="text-right">Rp {{ number_format($trx['dp'], 0, ',', '.') }}</td>
                                        <td class="text-right" style="color: #ea580c; font-weight: 600;">{{ $trx['sisa'] > 0 ? 'Rp ' . number_format($trx['sisa'], 0, ',', '.') : '-' }}</td>
                                        <td class="text-center" style="background-color: rgba(0,0,0,0.02);">-</td>
                                        <td class="text-center" style="background-color: rgba(0,0,0,0.02);">-</td>
                                        <td>{{ $trx['frame'] }}</td>
                                        <td>{{ $trx['lensa'] }}</td>
                                    </tr>
                                @endforeach
                            @endif

                            {{-- Daily Summary Row --}}
                            <tr class="row-subtotal">
                                <td colspan="4" class="text-right" style="font-size:11px; color:#6b7280; padding-top:10px; padding-bottom:10px; vertical-align: bottom;">
                                    TOTAL
                                </td>
                                <td class="text-right val-primary" style="vertical-align: bottom; padding-top:10px; padding-bottom:10px;">
                                    Rp {{ number_format($row['total_harga'] ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="text-right val-primary" style="vertical-align: bottom; padding-top:10px; padding-bottom:10px;">
                                    Rp {{ number_format($row['total_dp'] ?? 0, 0, ',', '.') }}
                                </td>
                                <td></td>
                                <td style="vertical-align: top; padding: 10px;">
                                    @forelse($row['pengeluaran_details'] as $detail)
                                        <div style="font-size: 11px; display: flex; justify-content: space-between; margin-bottom: 4px;">
                                            <span style="color: #4b5563;">{{ $detail['label'] }}</span>
                                            <span class="val-expense">Rp {{ number_format($detail['nominal'], 0, ',', '.') }}</span>
                                        </div>
                                    @empty
                                        <div style="font-size: 11px; color: #9ca3af; font-style: italic;">Tidak ada pengeluaran</div>
                                    @endforelse
                                    
                                    <div style="border-top: 1px solid #d1d5db; margin-top: 6px; padding-top: 6px; display: flex; justify-content: space-between; font-weight: 700; font-size: 12px;">
                                        <span style="font-size:11px; color:#6b7280;">TOTAL</span>
                                        <span class="val-expense-total">Rp {{ number_format($row['total_pengeluaran'], 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="text-right val-saldo" style="vertical-align: bottom; font-size: 14px; padding-top:10px; padding-bottom:10px;">
                                    Rp {{ number_format($row['saldo_akhir'], 0, ',', '.') }}
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center no-data">
                                    Tidak ada data transaksi atau pengeluaran pada rentang tanggal terpilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
