<?php

namespace App\Filament\Admin\Resources\BarangKeluar\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class BarangKeluarForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Utama')
                    ->schema([
                        Select::make('patient_id')
                            ->relationship('patient', 'nama')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->nama} - " . ($record->no_bon ?? 'Tanpa BON'))
                            ->label('Pasien')
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateHydrated(function (Get $get, Set $set, $state) {
                                if ($state) {
                                    $patient = \App\Models\Patient::find($state);
                                    if ($patient) {
                                        $set('no_bon', $patient->no_bon);
                                    }
                                }
                            })
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                if ($state) {
                                    $patient = \App\Models\Patient::find($state);
                                    if ($patient) {
                                        $set('no_bon', $patient->no_bon);
                                        
                                        $potongan = 0;
                                        if ($patient->kategori === 'BPJS Kelas 1') $potongan = 330000;
                                        elseif ($patient->kategori === 'BPJS Kelas 2') $potongan = 220000;
                                        elseif ($patient->kategori === 'BPJS Kelas 3') $potongan = 165000;
                                        
                                        $set('potongan_bpjs', $potongan);
                                        self::updateTotals($get, $set);
                                    }
                                }
                            }),
                        TextInput::make('no_bon')
                            ->label('No BON')
                            ->readOnly()
                            ->dehydrated()
                            ->placeholder('Otomatis dari Data Pasien'),
                        DatePicker::make('tanggal_transaksi')
                            ->label('Tanggal Pembuatan')
                            ->default(now())
                            ->required(),
                        DatePicker::make('tanggal_pengambilan')
                            ->label('Tanggal Pengambilan')
                            ->native(false),
                        Select::make('tipe_transaksi')
                            ->label('Jenis Pembelian')
                            ->options([
                                'frame' => 'Hanya Frame',
                                'lensa' => 'Hanya Lensa',
                                'lengkap' => 'Frame & Lensa (Lengkap)',
                                'aksesoris' => 'Hanya Aksesoris',
                            ])
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::updateTotals($get, $set);
                            })
                            ->required(),
                    ])->columns(['sm' => 1, 'md' => 2, 'lg' => 3])->columnSpanFull(),

                Section::make('Detail Frame')
                    ->schema([
                        Select::make('frame_id')
                            ->relationship('frame', 'name')
                            ->label('Pilih Frame')
                            ->searchable()
                            ->required(fn (Get $get) => in_array($get('tipe_transaksi'), ['frame', 'lengkap']))
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                if ($state) {
                                    $frame = \App\Models\Frame::find($state);
                                    if ($frame) {
                                        $set('harga_frame', $frame->harga_jual);
                                        self::updateTotals($get, $set);
                                    }
                                }
                            })
                            ->rules([
                                fn (Get $get, $record) => function (string $attribute, $value, $fail) use ($get, $record) {
                                    if (!$value || !in_array($get('tipe_transaksi'), ['frame', 'lengkap'])) {
                                        return;
                                    }
                                    
                                    $frame = \App\Models\Frame::find($value);
                                    if (!$frame) {
                                        return;
                                    }
                                    
                                    $barangMasuk = $frame->barangMasuk;
                                    $currentStok = $barangMasuk ? $barangMasuk->stok : 0;
                                    
                                    if ($record && $record->exists && $record->frame_id == $value) {
                                        $currentStok += 1;
                                    }
                                    
                                    if (1 > $currentStok) {
                                        $fail("Stok frame '{$frame->name}' kosong / tidak mencukupi.");
                                    }
                                }
                            ]),
                        TextInput::make('harga_frame')
                            ->label('Harga Frame')
                            ->numeric()
                            ->default(0)
                            ->readOnly(),
                    ])
                    ->columns(['sm' => 1, 'md' => 2, 'lg' => 3])
                    ->columnSpanFull()
                    ->visible(fn (Get $get) => in_array($get('tipe_transaksi'), ['frame', 'lengkap'])),

                Section::make('Detail Lensa')
                    ->schema([
                        Select::make('lens_id')
                            ->relationship('lens', 'name', fn ($query) => $query->with(['lensType', 'lensOwnershipCategory']))
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} - " . ($record->lensType?->name ?? 'Tanpa Jenis') . " - " . ($record->lensOwnershipCategory?->name ?? 'Tanpa Kategori'))
                            ->label('Pilih Lensa')
                            ->searchable()
                            ->required(fn (Get $get) => in_array($get('tipe_transaksi'), ['lensa', 'lengkap']))
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                if ($state) {
                                    $lens = \App\Models\Lens::with('lensOwnershipCategory')->find($state);
                                    if ($lens) {
                                        $pcs = (int) ($get('jumlah_lensa_pcs') ?: 2);
                                        $pairs = (int) ceil($pcs / 2);
                                        $hargaTotalLensa = $lens->harga_jual * $pairs;
                                        $set('harga_lensa', $hargaTotalLensa);
                                        
                                        if ($lens->lensOwnershipCategory?->type !== 'Luar Optik') {
                                            $set('biaya_beli_lensa', 0);
                                        }
                                        
                                        self::updateTotals($get, $set);
                                    }
                                }
                            }),
                        Select::make('jumlah_lensa_pcs')
                            ->label('Jumlah Pasang Lensa')
                            ->options([
                                1 => '0.5 Pasang (1 Pcs)',
                                2 => '1 Pasang (2 Pcs)',
                                3 => '1.5 Pasang (3 Pcs)',
                                4 => '2 Pasang (4 Pcs)',
                            ])
                            ->default(2)
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                $lensId = $get('lens_id');
                                if ($lensId) {
                                    $lens = \App\Models\Lens::find($lensId);
                                    if ($lens) {
                                        $pcs = (int) $state;
                                        $pairs = (int) ceil($pcs / 2);
                                        $hargaTotalLensa = $lens->harga_jual * $pairs;
                                        $set('harga_lensa', $hargaTotalLensa);
                                        self::updateTotals($get, $set);
                                    }
                                }
                            })
                            ->required(fn (Get $get) => in_array($get('tipe_transaksi'), ['lensa', 'lengkap']))
                            ->rules([
                                fn (Get $get, $record) => function (string $attribute, $value, $fail) use ($get, $record) {
                                    $lensId = $get('lens_id');
                                    if (!$lensId || !in_array($get('tipe_transaksi'), ['lensa', 'lengkap'])) {
                                        return;
                                    }
                                    
                                    $lens = \App\Models\Lens::find($lensId);
                                    if (!$lens) {
                                        return;
                                    }
                                    
                                    $barangMasuk = $lens->barangMasuk;
                                    $currentStok = $barangMasuk ? $barangMasuk->stok : 0;
                                    
                                    if ($record && $record->exists && $record->lens_id == $lensId) {
                                        $currentStok += (int) $record->jumlah_lensa_pcs;
                                    }
                                    
                                    $requestedPcs = (int) $value;
                                    if ($requestedPcs > $currentStok) {
                                        $fail("Stok lensa '{$lens->name}' tidak mencukupi. Stok tersedia: " . ($currentStok / 2) . " pasang (" . $currentStok . " pcs).");
                                    }
                                }
                            ]),
                        TextInput::make('harga_lensa')
                            ->label('Harga Lensa (Termasuk Pasang)')
                            ->numeric()
                            ->default(0)
                            ->readOnly(),
                        TextInput::make('biaya_beli_lensa')
                            ->label('Biaya Pembelian Lensa (Luar Optik)')
                            ->numeric()
                            ->default(0)
                            ->prefix('Rp')
                            ->helperText('Biaya pembelian/produksi lensa dari supplier luar optik. Nilai ini tidak memotong total transaksi pasien melainkan dicatat sebagai pengeluaran.')
                            ->visible(function (Get $get) {
                                $lensId = $get('lens_id');
                                if (!$lensId) return false;
                                $lens = \App\Models\Lens::with('lensOwnershipCategory')->find($lensId);
                                return $lens?->lensOwnershipCategory?->type === 'Luar Optik';
                            }),
                        TextInput::make('warna_lensa')
                            ->label('Pilihan Warna Lensa')
                            ->placeholder('Contoh: Hitam 50%, Coklat, Gradasi Blue')
                            ->required(function (Get $get) {
                                $lensId = $get('lens_id');
                                if ($lensId) {
                                    $lens = \App\Models\Lens::with('lensType')->find($lensId);
                                    return $lens && $lens->lensType?->name === 'Warna';
                                }
                                return false;
                            })
                            ->visible(function (Get $get) {
                                $lensId = $get('lens_id');
                                if ($lensId) {
                                    $lens = \App\Models\Lens::with('lensType')->find($lensId);
                                    return $lens && $lens->lensType?->name === 'Warna';
                                }
                                return false;
                            }),

                        Section::make('Ukuran Lensa')
                            ->schema([
                                // Header row (labels) using a Grid with 6 columns
                                Grid::make(6)
                                    ->schema([
                                        TextInput::make('label_mata')
                                            ->label('')
                                            ->default('MATA')
                                            ->readOnly()
                                            ->disabled()
                                            ->dehydrated(false),
                                        TextInput::make('label_sph')
                                            ->label('')
                                            ->default('SPH')
                                            ->readOnly()
                                            ->disabled()
                                            ->dehydrated(false),
                                        TextInput::make('label_cyl')
                                            ->label('')
                                            ->default('CYL')
                                            ->readOnly()
                                            ->disabled()
                                            ->dehydrated(false),
                                        TextInput::make('label_axis')
                                            ->label('')
                                            ->default('AXIS')
                                            ->readOnly()
                                            ->disabled()
                                            ->dehydrated(false),
                                        TextInput::make('label_add')
                                            ->label('')
                                            ->default('ADD')
                                            ->readOnly()
                                            ->disabled()
                                            ->dehydrated(false),
                                        TextInput::make('label_pd')
                                            ->label('')
                                            ->default('PD')
                                            ->readOnly()
                                            ->disabled()
                                            ->dehydrated(false),
                                    ])
                                    ->columns(6)
                                    ->columnSpanFull()
                                    ->extraAttributes(['class' => 'font-bold text-center']),

                                // OD Row
                                Grid::make(6)
                                    ->schema([
                                        TextInput::make('label_od')
                                            ->label('')
                                            ->default('OD (Kanan)')
                                            ->readOnly()
                                            ->disabled()
                                            ->dehydrated(false),
                                        TextInput::make('od_sph')
                                            ->label('')
                                            ->placeholder('- / + SPH'),
                                        TextInput::make('od_cyl')
                                            ->label('')
                                            ->placeholder('CYL'),
                                        TextInput::make('od_axis')
                                            ->label('')
                                            ->placeholder('AXIS'),
                                        TextInput::make('od_add')
                                            ->label('')
                                            ->placeholder('ADD'),
                                        TextInput::make('od_pd')
                                            ->label('')
                                            ->placeholder('PD'),
                                    ])
                                    ->columns(6)
                                    ->columnSpanFull(),

                                // OS Row
                                Grid::make(6)
                                    ->schema([
                                        TextInput::make('label_os')
                                            ->label('')
                                            ->default('OS (Kiri)')
                                            ->readOnly()
                                            ->disabled()
                                            ->dehydrated(false),
                                        TextInput::make('os_sph')
                                            ->label('')
                                            ->placeholder('- / + SPH'),
                                        TextInput::make('os_cyl')
                                            ->label('')
                                            ->placeholder('CYL'),
                                        TextInput::make('os_axis')
                                            ->label('')
                                            ->placeholder('AXIS'),
                                        TextInput::make('os_add')
                                            ->label('')
                                            ->placeholder('ADD'),
                                        TextInput::make('os_pd')
                                            ->label('')
                                            ->placeholder('PD'),
                                    ])
                                    ->columns(6)
                                    ->columnSpanFull(),
                            ])
                            ->columnSpanFull(),
                        
                        Section::make('Lainnya')
                            ->schema([
                                TextInput::make('index_bias')->label('Index Bias'),
                                TagsInput::make('aksesoris')
                                    ->label('Aksesoris')
                                    ->suggestions(['Facet', 'Bor', 'Gosok', 'Warna/Tint']),
                            ])->columns(['sm' => 1, 'md' => 2])->columnSpanFull(),
                    ])
                    ->columns(['sm' => 1, 'md' => 2, 'lg' => 3])
                    ->columnSpanFull()
                    ->visible(fn (Get $get) => in_array($get('tipe_transaksi'), ['lensa', 'lengkap'])),

                 Section::make('Pembelian Aksesoris')
                    ->description('Pilih aksesoris tambahan yang dibeli oleh pasien')
                    ->schema([
                        Repeater::make('barangKeluarAccessories')
                            ->relationship('barangKeluarAccessories')
                            ->schema([
                                Select::make('accessory_id')
                                    ->label('Pilih Aksesoris')
                                    ->options(\App\Models\Accessory::pluck('nama', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                        if ($state) {
                                            $acc = \App\Models\Accessory::find($state);
                                            if ($acc) {
                                                $qty = (int) ($get('qty') ?: 1);
                                                $set('harga_jual_satuan', $acc->harga_jual);
                                                $set('subtotal_jual', $acc->harga_jual * $qty);
                                                $set('harga_beli_satuan', $acc->harga_beli);
                                                $set('subtotal_beli', $acc->harga_beli * $qty);
                                            }
                                        }
                                    }),
                                TextInput::make('qty')
                                    ->label('Jumlah (Qty)')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                        $accId = $get('accessory_id');
                                        if ($accId) {
                                            $acc = \App\Models\Accessory::find($accId);
                                            if ($acc) {
                                                $qty = (int) ($state ?: 1);
                                                $set('harga_jual_satuan', $acc->harga_jual);
                                                $set('subtotal_jual', $acc->harga_jual * $qty);
                                                $set('harga_beli_satuan', $acc->harga_beli);
                                                $set('subtotal_beli', $acc->harga_beli * $qty);
                                            }
                                        }
                                    }),
                                TextInput::make('harga_jual_satuan')
                                    ->label('Harga Satuan')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->readOnly()
                                    ->dehydrated(),
                                TextInput::make('subtotal_jual')
                                    ->label('Total Harga')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->readOnly()
                                    ->dehydrated(),
                                Hidden::make('harga_beli_satuan')
                                    ->dehydrated(),
                                Hidden::make('subtotal_beli')
                                    ->dehydrated(),
                            ])
                            ->columns(4)
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::updateTotals($get, $set);
                            })
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                Section::make('Pembayaran')
                    ->schema([
                         TextInput::make('potongan_bpjs')
                            ->label('Potongan BPJS')
                            ->numeric()
                            ->default(0)
                            ->readOnly(),
                        TextInput::make('sisa_bpjs')
                            ->label('Sisa Uang Kembalian BPJS')
                            ->numeric()
                            ->default(0)
                            ->readOnly()
                            ->prefix('Rp')
                            ->dehydrated(),
                        TextInput::make('diskon')
                            ->label('Diskon Lainnya')
                            ->numeric()
                            ->default(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                self::updateTotals($get, $set);
                            }),
                        TextInput::make('tambahan_biaya')
                            ->label('Tambahan Biaya')
                            ->numeric()
                            ->default(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::updateTotals($get, $set);
                            }),
                        TextInput::make('total_transaksi')
                            ->label('Total Transaksi')
                            ->numeric()
                            ->default(0)
                            ->readOnly()
                            ->required(),
                        Select::make('status_pembayaran')
                            ->label('Status Pembayaran')
                            ->options([
                                'lunas' => 'Lunas',
                                'dp' => 'DP (Sebagian)',
                                'belum_bayar' => 'Belum Bayar',
                            ])
                            ->live()
                            ->default('belum_bayar')
                            ->required(),
                        Select::make('metode_pembayaran')
                            ->label('Metode Pembayaran')
                            ->options([
                                'Cash' => 'Cash (Tunai)',
                                'Transfer' => 'Transfer (TF)',
                            ])
                            ->default('Cash')
                            ->required(),
                        TextInput::make('dp_dibayar')
                            ->label('Nominal DP / Dibayar')
                            ->numeric()
                            ->default(0)
                            ->visible(fn (Get $get) => $get('status_pembayaran') === 'dp'),
                    ])->columns(['sm' => 1, 'md' => 2, 'lg' => 3])->columnSpanFull(),
            ]);
    }

    public static function updateTotals(Get $get, Set $set): void
    {
        $tipe = $get('tipe_transaksi');
        $frame = in_array($tipe, ['frame', 'lengkap']) ? ((float) $get('harga_frame') ?: 0) : 0;
        $lensa = in_array($tipe, ['lensa', 'lengkap']) ? ((float) $get('harga_lensa') ?: 0) : 0;
        $tambahanBiaya = (float) $get('tambahan_biaya') ?: 0;
        
        $items = $get('barangKeluarAccessories') ?? [];
        $totalAksesoris = 0.0;
        if (is_array($items)) {
            foreach ($items as $item) {
                $totalAksesoris += (float) ($item['subtotal_jual'] ?? 0);
            }
        }

        $potongan = (float) $get('potongan_bpjs') ?: 0;
        $diskon = (float) $get('diskon') ?: 0;

        // Diskon memotong harga kacamata (Frame + Lensa + Tambahan Biaya + Aksesoris) terlebih dahulu
        $adjustedPrice = max(0.0, ($frame + $lensa + $tambahanBiaya + $totalAksesoris) - $diskon);

        $sisaBpjs = 0.0;
        if ($potongan > 0 && $adjustedPrice < $potongan) {
            $sisaBpjs = $potongan - $adjustedPrice;
        }

        $set('sisa_bpjs', $sisaBpjs);
        $set('total_transaksi', max(0.0, $adjustedPrice - $potongan));
    }
}
