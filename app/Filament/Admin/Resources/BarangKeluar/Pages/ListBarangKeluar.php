<?php

namespace App\Filament\Admin\Resources\BarangKeluar\Pages;

use App\Filament\Admin\Resources\BarangKeluar\BarangKeluarResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Carbon;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Get;

class ListBarangKeluar extends ListRecords
{
    protected static string $resource = BarangKeluarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // ── Tombol Cetak Laporan Frame Keluar 10 Hari ──
            Action::make('cetak_frame_10_hari')
                ->label('Cetak Laporan Frame 10 Hari')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->form([
                    DatePicker::make('tanggal_awal')
                        ->label('Tanggal Awal')
                        ->required()
                        ->live()
                        ->default(Carbon::now()->format('Y-m-d')),
                    DatePicker::make('tanggal_akhir')
                        ->label('Tanggal Akhir')
                        ->required()
                        ->minDate(fn ($get) => $get('tanggal_awal'))
                        ->maxDate(fn ($get) => $get('tanggal_awal') ? Carbon::parse($get('tanggal_awal'))->addDays(9)->format('Y-m-d') : null)
                        ->default(fn ($get) => $get('tanggal_awal') ? Carbon::parse($get('tanggal_awal'))->addDays(9)->format('Y-m-d') : Carbon::now()->format('Y-m-d')),
                ])
                ->modalHeading('Cetak Laporan Frame Keluar 10 Hari')
                ->modalDescription('Pilih range tanggal maksimal 10 hari untuk mencetak laporan.')
                ->modalSubmitActionLabel('Cetak Laporan')
                ->modalIcon('heroicon-o-document-text')
                ->action(function (array $data) {
                    $url = route('frame-keluar.print-10-hari', [
                        'tanggal_awal' => $data['tanggal_awal'],
                        'tanggal_akhir' => $data['tanggal_akhir'],
                    ]);
                    // Buka di tab baru via JS
                    $this->js("window.open('{$url}', '_blank')");
                }),

            // ── Tombol Cetak Laporan Frame Keluar Bulanan ──
            Action::make('cetak_frame_bulanan')
                ->label('Cetak Laporan Frame Bulanan')
                ->icon('heroicon-o-printer')
                ->color('warning')
                ->form([
                    Select::make('bulan')
                        ->label('Bulan')
                        ->options([
                            1  => 'Januari',
                            2  => 'Februari',
                            3  => 'Maret',
                            4  => 'April',
                            5  => 'Mei',
                            6  => 'Juni',
                            7  => 'Juli',
                            8  => 'Agustus',
                            9  => 'September',
                            10 => 'Oktober',
                            11 => 'November',
                            12 => 'Desember',
                        ])
                        ->default(Carbon::now()->month)
                        ->required()
                        ->native(false),
                    TextInput::make('tahun')
                        ->label('Tahun')
                        ->numeric()
                        ->default(Carbon::now()->year)
                        ->minValue(2020)
                        ->maxValue(2099)
                        ->required(),
                ])
                ->modalHeading('Cetak Laporan Frame Keluar Bulanan')
                ->modalDescription('Pilih bulan dan tahun untuk mencetak laporan frame yang keluar.')
                ->modalSubmitActionLabel('Cetak Laporan')
                ->modalIcon('heroicon-o-document-text')
                ->action(function (array $data) {
                    $url = route('frame-keluar.print-bulanan', [
                        'bulan' => $data['bulan'],
                        'tahun' => $data['tahun'],
                    ]);
                    // Buka di tab baru via JS
                    $this->js("window.open('{$url}', '_blank')");
                }),

            // ── Tombol Cetak Laporan Lensa ──
            Action::make('cetak_lensa')
                ->label('Cetak Laporan Lensa')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->form([
                    Select::make('bulan')
                        ->label('Bulan')
                        ->options([
                            1  => 'Januari',
                            2  => 'Februari',
                            3  => 'Maret',
                            4  => 'April',
                            5  => 'Mei',
                            6  => 'Juni',
                            7  => 'Juli',
                            8  => 'Agustus',
                            9  => 'September',
                            10 => 'Oktober',
                            11 => 'November',
                            12 => 'Desember',
                        ])
                        ->default(Carbon::now()->month)
                        ->required()
                        ->native(false),
                    TextInput::make('tahun')
                        ->label('Tahun')
                        ->numeric()
                        ->default(Carbon::now()->year)
                        ->minValue(2020)
                        ->maxValue(2099)
                        ->required(),
                    Select::make('jenis_kepemilikan')
                        ->label('Kategori Kepemilikan Lensa')
                        ->options([
                            'Stok Optik' => 'Stok Optik',
                            'Luar Optik' => 'Luar Optik',
                        ])
                        ->default('Stok Optik')
                        ->required()
                        ->native(false),
                ])
                ->modalHeading('Cetak Laporan Lensa Keluar')
                ->modalDescription('Pilih bulan, tahun, dan jenis kepemilikan lensa untuk mencetak laporan.')
                ->modalSubmitActionLabel('Cetak Laporan')
                ->modalIcon('heroicon-o-document-text')
                ->action(function (array $data) {
                    $url = route('lensa-keluar.print', [
                        'bulan' => $data['bulan'],
                        'tahun' => $data['tahun'],
                        'jenis_kepemilikan' => $data['jenis_kepemilikan'],
                    ]);
                    // Buka di tab baru via JS
                    $this->js("window.open('{$url}', '_blank')");
                }),

            CreateAction::make(),
        ];
    }
}
