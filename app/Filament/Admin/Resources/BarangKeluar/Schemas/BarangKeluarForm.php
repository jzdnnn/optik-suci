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
                            ->required(),
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
                                        $set('total_transaksi', $frame->harga_jual + $lensa);
                                    }
                                }
                            }),
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
                                        $set('total_transaksi', $frame + $hargaTotalLensa);
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
                                        $set('total_transaksi', $frame + $hargaTotalLensa);
                                    }
                                }
                            })
                            ->required(fn (Get $get) => in_array($get('tipe_transaksi'), ['lensa', 'lengkap'])),
                        TextInput::make('harga_lensa')
                            ->label('Harga Lensa (Termasuk Pasang)')
                            ->numeric()
                            ->default(0)
                            ->readOnly(),
                        
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
