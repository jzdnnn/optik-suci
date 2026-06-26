<?php

namespace App\Filament\Admin\Resources\LaporanBulanan\Schemas;

use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class LaporanBulananForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Laporan Bulanan')
                    ->tabs([
                        Tab::make('Informasi & Pendapatan')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make('Identitas Laporan')
                                    ->description('Bulan, Tahun, dan Cabang Optik')
                                    ->schema([
                                        Select::make('bulan')
                                            ->label('Bulan')
                                            ->options([
                                                1 => 'Januari',
                                                2 => 'Februari',
                                                3 => 'Maret',
                                                4 => 'April',
                                                5 => 'Mei',
                                                6 => 'Juni',
                                                7 => 'Juli',
                                                8 => 'Agustus',
                                                9 => 'September',
                                                10 => 'Oktober',
                                                11 => 'November',
                                                12 => 'Desember',
                                            ])
                                            ->required()
                                            ->live()
                                            ->afterStateHydrated(fn (Get $get, Set $set) => self::updateCalculatedFields($get, $set))
                                            ->afterStateUpdated(fn (Get $get, Set $set) => self::updateCalculatedFields($get, $set)),
                                        TextInput::make('tahun')
                                            ->label('Tahun')
                                            ->numeric()
                                            ->default(date('Y'))
                                            ->required()
                                            ->live()
                                            ->afterStateHydrated(fn (Get $get, Set $set) => self::updateCalculatedFields($get, $set))
                                            ->afterStateUpdated(fn (Get $get, Set $set) => self::updateCalculatedFields($get, $set)),
                                        TextInput::make('cabang')
                                            ->label('Cabang Optik')
                                            ->default(fn () => session('cabang_nama', ''))
                                            ->readOnly()
                                            ->dehydrated()
                                            ->required()
                                            ->live()
                                            ->afterStateHydrated(fn (Get $get, Set $set) => self::updateCalculatedFields($get, $set))
                                            ->afterStateUpdated(fn (Get $get, Set $set) => self::updateCalculatedFields($get, $set)),
                                    ])
                                    ->columns(3),

                                Section::make('A. Pendapatan')
                                    ->description('Nominal Omzet dan Pendapatan Harian dihitung otomatis dari transaksi barang keluar pada bulan dan tahun terpilih.')
                                    ->schema([
                                        TextInput::make('omzet')
                                            ->label('Omzet')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->default(0)
                                            ->readOnly()
                                            ->dehydrated(),
                                        TextInput::make('pendapatan_bpjs')
                                            ->label('Pendapatan BPJS')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->default(0)
                                            ->live(),
                                        TextInput::make('pendapatan_harian')
                                            ->label('Pendapatan Harian (Tunai Masuk)')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->default(0)
                                            ->readOnly()
                                            ->dehydrated(),
                                    ])
                                    ->columns(3),

                                 Section::make('C. Setoran Mingguan')
                                    ->description('Nominal setoran per minggu dihitung otomatis dari transaksi setoran mingguan di menu Setoran Mingguan.')
                                    ->schema([
                                        TextInput::make('setoran_minggu_1')
                                            ->label('Minggu 1')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->default(0)
                                            ->readOnly()
                                            ->dehydrated(),
                                        TextInput::make('setoran_minggu_2')
                                            ->label('Minggu 2')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->default(0)
                                            ->readOnly()
                                            ->dehydrated(),
                                        TextInput::make('setoran_minggu_3')
                                            ->label('Minggu 3')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->default(0)
                                            ->readOnly()
                                            ->dehydrated(),
                                        TextInput::make('setoran_minggu_4')
                                            ->label('Minggu 4')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->default(0)
                                            ->readOnly()
                                            ->dehydrated(),
                                        TextInput::make('setoran_minggu_5')
                                            ->label('Minggu 5')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->default(0)
                                            ->readOnly()
                                            ->dehydrated(),
                                    ])
                                    ->columns(5),
                            ]),

                        Tab::make('B. Rincian Selisih')
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                Section::make('Data Selisih Pelunasan')
                                    ->description('Daftar selisih bon/pelunasan bulan ini')
                                    ->schema([
                                        Repeater::make('selisih_details')
                                            ->label('Daftar Selisih')
                                            ->schema([
                                                TextInput::make('no_bon')
                                                    ->label('Nomor Bon')
                                                    ->readOnly()
                                                    ->required(),
                                                TextInput::make('nama_pegawai')
                                                    ->label('Nama Pegawai')
                                                    ->readOnly()
                                                    ->required(),
                                                Hidden::make('original_status'),
                                                Select::make('keterangan')
                                                    ->label('Keterangan')
                                                    ->options(function (Get $get) {
                                                        $original = $get('original_status');
                                                        $current = $get('keterangan');

                                                        $options = [];
                                                        if ($original === 'dp' || $current === 'Sisa Pelunasan DP') {
                                                            $options['Sisa Pelunasan DP'] = 'Sisa Pelunasan DP';
                                                        }
                                                        if ($original === 'belum_bayar' || $current === 'Belum Bayar (Belum Lunas)') {
                                                            $options['Belum Bayar (Belum Lunas)'] = 'Belum Bayar (Belum Lunas)';
                                                        }
                                                        $options['Lunas'] = 'Lunas';
                                                        return $options;
                                                    })
                                                    ->required()
                                                    ->live()
                                                    ->afterStateUpdated(function ($state, Set $set) {
                                                        if ($state === 'Lunas') {
                                                            $set('nominal', 0);
                                                        }
                                                    }),
                                                TextInput::make('nominal')
                                                    ->label('Nominal')
                                                    ->numeric()
                                                    ->prefix('Rp')
                                                    ->required()
                                                    ->live(),
                                            ])
                                            ->columns(4)
                                            ->itemLabel(fn (array $state): ?string => ($state['no_bon'] ?? null) ? "Bon: {$state['no_bon']} - Rp " . number_format($state['nominal'] ?? 0, 0, ',', '.') : null)
                                            ->default([])
                                            ->live()
                                            ->addable(false)
                                            ->deletable(false),
                                    ])
                            ]),

                        Tab::make('Ringkasan Pengeluaran & Laba')
                            ->icon('heroicon-o-arrow-trending-down')
                            ->schema([
                                Section::make('Rangkuman Biaya Pengeluaran')
                                    ->description('Nilai pengeluaran di bawah ini dihitung otomatis dari transaksi pengeluaran yang dicatat di menu Catat Pengeluaran.')
                                    ->schema([
                                        Placeholder::make('total_pengeluaran_operasional')
                                            ->label('Total Pengeluaran Operasional Harian')
                                            ->content(function (Get $get) {
                                                $cabang = $get('cabang');
                                                $bulan = $get('bulan');
                                                $tahun = $get('tahun');
                                                if (!$cabang || !$bulan || !$tahun) return 'Rp 0';
                                                
                                                $sum = \App\Models\Pengeluaran::join('jenis_pengeluaran', 'pengeluaran.jenis_pengeluaran_id', '=', 'jenis_pengeluaran.id')
                                                    ->where('pengeluaran.cabang', $cabang)
                                                    ->whereYear('pengeluaran.tanggal', $tahun)
                                                    ->whereMonth('pengeluaran.tanggal', $bulan)
                                                    ->where('jenis_pengeluaran.tipe', 'operasional')
                                                    ->sum('pengeluaran.nominal');
                                                return 'Rp ' . number_format($sum, 0, ',', '.');
                                            }),
                                        Placeholder::make('total_pengeluaran_stok')
                                            ->label('Total Pengeluaran Stok & Persediaan')
                                            ->content(function (Get $get) {
                                                $cabang = $get('cabang');
                                                $bulan = $get('bulan');
                                                $tahun = $get('tahun');
                                                if (!$cabang || !$bulan || !$tahun) return 'Rp 0';
                                                
                                                $sum = \App\Models\Pengeluaran::join('jenis_pengeluaran', 'pengeluaran.jenis_pengeluaran_id', '=', 'jenis_pengeluaran.id')
                                                    ->where('pengeluaran.cabang', $cabang)
                                                    ->whereYear('pengeluaran.tanggal', $tahun)
                                                    ->whereMonth('pengeluaran.tanggal', $bulan)
                                                    ->where('jenis_pengeluaran.tipe', 'stok')
                                                    ->sum('pengeluaran.nominal');
                                                return 'Rp ' . number_format($sum, 0, ',', '.');
                                            }),
                                        Placeholder::make('total_pengeluaran_gaji')
                                            ->label('Total Pengeluaran Gaji & Lainnya')
                                            ->content(function (Get $get) {
                                                $cabang = $get('cabang');
                                                $bulan = $get('bulan');
                                                $tahun = $get('tahun');
                                                if (!$cabang || !$bulan || !$tahun) return 'Rp 0';
                                                
                                                $sum = \App\Models\Pengeluaran::join('jenis_pengeluaran', 'pengeluaran.jenis_pengeluaran_id', '=', 'jenis_pengeluaran.id')
                                                    ->where('pengeluaran.cabang', $cabang)
                                                    ->whereYear('pengeluaran.tanggal', $tahun)
                                                    ->whereMonth('pengeluaran.tanggal', $bulan)
                                                    ->where('jenis_pengeluaran.tipe', 'gaji')
                                                    ->sum('pengeluaran.nominal');
                                                return 'Rp ' . number_format($sum, 0, ',', '.');
                                            }),
                                        Placeholder::make('total_seluruh_pengeluaran')
                                            ->label('Total Seluruh Pengeluaran (A + B + D)')
                                            ->content(function (Get $get) {
                                                $cabang = $get('cabang');
                                                $bulan = $get('bulan');
                                                $tahun = $get('tahun');
                                                if (!$cabang || !$bulan || !$tahun) return 'Rp 0';
                                                
                                                $sum = \App\Models\Pengeluaran::join('jenis_pengeluaran', 'pengeluaran.jenis_pengeluaran_id', '=', 'jenis_pengeluaran.id')
                                                    ->where('pengeluaran.cabang', $cabang)
                                                    ->whereYear('pengeluaran.tanggal', $tahun)
                                                    ->whereMonth('pengeluaran.tanggal', $bulan)
                                                    ->sum('pengeluaran.nominal');
                                                return 'Rp ' . number_format($sum, 0, ',', '.');
                                            }),
                                        Placeholder::make('laba_bersih')
                                            ->label('LABA BERSIH BULANAN')
                                            ->content(function (Get $get) {
                                                $cabang = $get('cabang');
                                                $bulan = $get('bulan');
                                                $tahun = $get('tahun');
                                                if (!$cabang || !$bulan || !$tahun) return 'Rp 0';

                                                $omzet = (float) $get('omzet');
                                                $bpjs = (float) $get('pendapatan_bpjs');
                                                $harian = (float) $get('pendapatan_harian');
                                                $totalPendapatan = $omzet + $bpjs + $harian;

                                                $totalSelisih = collect($get('selisih_details') ?? [])
                                                    ->sum(fn ($item) => (float) ($item['nominal'] ?? 0));

                                                $totalPengeluaran = \App\Models\Pengeluaran::join('jenis_pengeluaran', 'pengeluaran.jenis_pengeluaran_id', '=', 'jenis_pengeluaran.id')
                                                    ->where('pengeluaran.cabang', $cabang)
                                                    ->whereYear('pengeluaran.tanggal', $tahun)
                                                    ->whereMonth('pengeluaran.tanggal', $bulan)
                                                    ->sum('pengeluaran.nominal');

                                                $laba = $totalPendapatan - $totalPengeluaran - $totalSelisih;
                                                return 'Rp ' . number_format($laba, 0, ',', '.');
                                            }),
                                    ])
                                    ->columns(2),

                                Section::make('Log Pengeluaran Detail')
                                    ->description('Daftar transaksi pengeluaran dari menu Catat Pengeluaran untuk cabang dan periode ini')
                                    ->schema([
                                        Placeholder::make('expense_details_list')
                                            ->label('')
                                            ->content(function (Get $get) {
                                                $cabang = $get('cabang');
                                                $bulan = $get('bulan');
                                                $tahun = $get('tahun');

                                                if (!$cabang || !$bulan || !$tahun) {
                                                    return 'Pilih Cabang, Bulan, dan Tahun terlebih dahulu untuk melihat log detail pengeluaran.';
                                                }

                                                $expenses = \App\Models\Pengeluaran::where('cabang', $cabang)
                                                    ->whereYear('tanggal', $tahun)
                                                    ->whereMonth('tanggal', $bulan)
                                                    ->with('jenisPengeluaran')
                                                    ->orderBy('tanggal')
                                                    ->get();

                                                if ($expenses->isEmpty()) {
                                                    return 'Tidak ada log pengeluaran yang tercatat untuk cabang dan periode ini.';
                                                }

                                                $html = '<div style="margin-top: 10px; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden;"><table style="width: 100%; border-collapse: collapse; text-align: left;">';
                                                $html .= '<thead style="background-color: #f9fafb;"><tr style="border-bottom: 1px solid #e5e7eb;">';
                                                $html .= '<th style="padding: 10px 12px; font-size: 11px; font-weight: 600; text-transform: uppercase; color: #4b5563;">Tanggal</th>';
                                                $html .= '<th style="padding: 10px 12px; font-size: 11px; font-weight: 600; text-transform: uppercase; color: #4b5563;">Kategori</th>';
                                                $html .= '<th style="padding: 10px 12px; font-size: 11px; font-weight: 600; text-transform: uppercase; color: #4b5563;">Nominal</th>';
                                                $html .= '<th style="padding: 10px 12px; font-size: 11px; font-weight: 600; text-transform: uppercase; color: #4b5563;">Keterangan</th>';
                                                $html .= '</tr></thead><tbody>';

                                                foreach ($expenses as $exp) {
                                                    $html .= '<tr style="border-bottom: 1px solid #f3f4f6;">';
                                                    $html .= '<td style="padding: 10px 12px; font-size: 13px; color: #374151;">' . $exp->tanggal->format('d M Y') . '</td>';
                                                    $html .= '<td style="padding: 10px 12px; font-size: 13px; color: #374151;">' . e($exp->jenisPengeluaran->nama) . '</td>';
                                                    $html .= '<td style="padding: 10px 12px; font-size: 13px; color: #374151; font-weight: 600;">Rp ' . number_format($exp->nominal, 0, ',', '.') . '</td>';
                                                    $html .= '<td style="padding: 10px 12px; font-size: 13px; color: #6b7280;">' . e($exp->keterangan ?? '-') . '</td>';
                                                    $html .= '</tr>';
                                                }

                                                $html .= '</tbody></table></div>';
                                                return new \Illuminate\Support\HtmlString($html);
                                            })
                                            ->columnSpanFull()
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull()
            ]);
    }

    public static function updateCalculatedFields(Get $get, Set $set): void
    {
        $bulan = $get('bulan');
        $tahun = $get('tahun');
        $cabang = $get('cabang');

        if (!$bulan || !$tahun) {
            $set('omzet', 0);
            $set('pendapatan_harian', 0);
            $set('selisih_details', []);
            for ($week = 1; $week <= 5; $week++) {
                $set("setoran_minggu_{$week}", 0);
            }
            return;
        }

        // Calculate Omzet
        $omzet = (float) \App\Models\BarangKeluar::whereYear('tanggal_transaksi', $tahun)
            ->whereMonth('tanggal_transaksi', $bulan)
            ->sum('total_transaksi');
        $set('omzet', $omzet);

        // Calculate Pendapatan Harian
        $transactions = \App\Models\BarangKeluar::with('patient')
            ->whereYear('tanggal_transaksi', $tahun)
            ->whereMonth('tanggal_transaksi', $bulan)
            ->get();

        $harian = (float) $transactions->sum(function ($item) {
            return $item->status_pembayaran === 'lunas' ? $item->total_transaksi : ($item->status_pembayaran === 'dp' ? $item->dp_dibayar : 0);
        });
        $set('pendapatan_harian', $harian);

        // Calculate weekly deposits
        for ($week = 1; $week <= 5; $week++) {
            $nominal = 0;
            if ($cabang) {
                $nominal = (float) \App\Models\SetoranMingguan::where('cabang', $cabang)
                    ->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', $bulan)
                    ->where('minggu_ke', $week)
                    ->sum('nominal');
            }
            $set("setoran_minggu_{$week}", $nominal);
        }

        // Auto populate B. Rincian Selisih for unpaid / dp transactions
        $selisihDetails = [];
        foreach ($transactions as $item) {
            if ($item->status_pembayaran === 'belum_bayar') {
                $selisihDetails[] = [
                    'no_bon' => $item->no_bon ?? '',
                    'nama_pegawai' => $item->patient?->nama ?? '-',
                    'keterangan' => 'Belum Bayar (Belum Lunas)',
                    'nominal' => (float) $item->total_transaksi,
                    'original_status' => 'belum_bayar',
                ];
            } elseif ($item->status_pembayaran === 'dp') {
                $selisih = (float) $item->total_transaksi - (float) $item->dp_dibayar;
                if ($selisih > 0) {
                    $selisihDetails[] = [
                        'no_bon' => $item->no_bon ?? '',
                        'nama_pegawai' => $item->patient?->nama ?? '-',
                        'keterangan' => 'Sisa Pelunasan DP',
                        'nominal' => $selisih,
                        'original_status' => 'dp',
                    ];
                }
            }
        }
        $set('selisih_details', $selisihDetails);
    }
}
