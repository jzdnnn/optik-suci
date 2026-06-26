<?php

namespace App\Filament\Admin\Resources\BarangKeluar\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\DatePicker;
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
                            ->label('Pasien')
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                if ($state) {
                                    $patient = \App\Models\Patient::find($state);
                                    if ($patient) {
                                        $potongan = 0;
                                        if ($patient->kategori === 'BPJS Kelas 1') $potongan = 330000;
                                        elseif ($patient->kategori === 'BPJS Kelas 2') $potongan = 220000;
                                        elseif ($patient->kategori === 'BPJS Kelas 3') $potongan = 165000;
                                        
                                        $set('potongan_bpjs', $potongan);
                                        
                                        $frame = (float) $get('harga_frame') ?: 0;
                                        $lensa = (float) $get('harga_lensa') ?: 0;
                                        $diskon = (float) $get('diskon') ?: 0;
                                        $set('total_transaksi', $frame + $lensa - $potongan - $diskon);
                                    }
                                }
                            }),
                        TextInput::make('no_bon')
                            ->label('No BON')
                            ->readOnly()
                            ->placeholder('Auto Generate (e.g. 001)'),
                        DatePicker::make('tanggal_transaksi')
                            ->label('Tanggal Transaksi')
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
                            ])
                            ->live()
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
                                        $lensa = (float) $get('harga_lensa') ?: 0;
                                        $potongan = (float) $get('potongan_bpjs') ?: 0;
                                        $diskon = (float) $get('diskon') ?: 0;
                                        $set('total_transaksi', $frame->harga_jual + $lensa - $potongan - $diskon);
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
                            ->relationship('lens', 'name')
                            ->label('Pilih Lensa')
                            ->searchable()
                            ->required(fn (Get $get) => in_array($get('tipe_transaksi'), ['lensa', 'lengkap']))
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                if ($state) {
                                    $lens = \App\Models\Lens::find($state);
                                    if ($lens) {
                                        $pcs = (int) ($get('jumlah_lensa_pcs') ?: 2);
                                        $hargaTotalLensa = $lens->harga_jual * $pcs;
                                        $set('harga_lensa', $hargaTotalLensa);
                                        $frame = (float) $get('harga_frame') ?: 0;
                                        $potongan = (float) $get('potongan_bpjs') ?: 0;
                                        $diskon = (float) $get('diskon') ?: 0;
                                        $set('total_transaksi', $frame + $hargaTotalLensa - $potongan - $diskon);
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
                                        $hargaTotalLensa = $lens->harga_jual * $pcs;
                                        $set('harga_lensa', $hargaTotalLensa);
                                        $frame = (float) $get('harga_frame') ?: 0;
                                        $potongan = (float) $get('potongan_bpjs') ?: 0;
                                        $diskon = (float) $get('diskon') ?: 0;
                                        $set('total_transaksi', $frame + $hargaTotalLensa - $potongan - $diskon);
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

                Section::make('Pembayaran')
                    ->schema([
                        TextInput::make('potongan_bpjs')
                            ->label('Potongan BPJS')
                            ->numeric()
                            ->default(0)
                            ->readOnly(),
                        TextInput::make('diskon')
                            ->label('Diskon Lainnya')
                            ->numeric()
                            ->default(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                $frame = (float) $get('harga_frame') ?: 0;
                                $lensa = (float) $get('harga_lensa') ?: 0;
                                $potongan = (float) $get('potongan_bpjs') ?: 0;
                                $diskon = (float) $state ?: 0;
                                $set('total_transaksi', $frame + $lensa - $potongan - $diskon);
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
                        TextInput::make('dp_dibayar')
                            ->label('Nominal DP / Dibayar')
                            ->numeric()
                            ->default(0)
                            ->visible(fn (Get $get) => $get('status_pembayaran') === 'dp'),
                    ])->columns(['sm' => 1, 'md' => 2, 'lg' => 3])->columnSpanFull(),
            ]);
    }
}
