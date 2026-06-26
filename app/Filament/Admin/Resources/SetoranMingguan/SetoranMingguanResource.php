<?php

namespace App\Filament\Admin\Resources\SetoranMingguan;

use App\Filament\Admin\Resources\SetoranMingguan\Pages\ManageSetoranMingguan;
use App\Models\SetoranMingguan;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class SetoranMingguanResource extends Resource
{
    protected static ?string $model = SetoranMingguan::class;

    protected static ?string $modelLabel = 'Setoran Mingguan';
    protected static ?string $pluralModelLabel = 'Setoran Mingguan';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-up-tray';
    protected static string|\UnitEnum|null $navigationGroup = 'Laporan';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('tanggal')
                    ->label('Tanggal Setoran')
                    ->required()
                    ->default(now())
                    ->live()
                    ->afterStateHydrated(function (Get $get, Set $set, $state) {
                        self::updateMingguKe($state, $set);
                    })
                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                        self::updateMingguKe($state, $set);
                    }),
                TextInput::make('cabang')
                    ->label('Cabang Optik')
                    ->default(fn () => session('cabang_nama', ''))
                    ->readOnly()
                    ->dehydrated()
                    ->required(),
                TextInput::make('minggu_ke')
                    ->label('Setoran Minggu Ke')
                    ->readOnly()
                    ->dehydrated()
                    ->required(),
                TextInput::make('nominal')
                    ->label('Nominal Setoran')
                    ->helperText(function (Get $get) {
                        $tanggal = $get('tanggal');
                        if (!$tanggal) return null;

                        try {
                            $carbonDate = \Illuminate\Support\Carbon::parse($tanggal);
                            $month = $carbonDate->month;
                            $year = $carbonDate->year;
                        } catch (\Exception $e) {
                            return null;
                        }

                        // Query total income for this month
                        $transactions = \App\Models\BarangKeluar::whereYear('tanggal_transaksi', $year)
                            ->whereMonth('tanggal_transaksi', $month)
                            ->get();
                        $totalCash = $transactions->sum(function ($item) {
                            return $item->status_pembayaran === 'lunas' ? $item->total_transaksi : ($item->status_pembayaran === 'dp' ? $item->dp_dibayar : 0);
                        });

                        return new \Illuminate\Support\HtmlString(
                            '<span style="color: #ef4444; font-size: 0.85em; font-weight: 500;">' .
                            'total penghasilan saat ini Rp. ' . number_format($totalCash, 0, ',', '.') .
                            '</span>'
                        );
                    })
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
                TextInput::make('keterangan')
                    ->label('Keterangan')
                    ->placeholder('Contoh: Setoran cash minggu 1')
                    ->maxLength(255),
            ]);
    }

    public static function updateMingguKe($date, Set $set): void
    {
        if (!$date) return;

        $day = null;
        if ($date instanceof \DateTimeInterface) {
            $day = (int) $date->format('j');
        } else {
            try {
                $day = (int) \Illuminate\Support\Carbon::parse($date)->format('j');
            } catch (\Exception $e) {
                try {
                    $day = (int) \Illuminate\Support\Carbon::createFromFormat('d/m/Y', $date)->format('j');
                } catch (\Exception $e2) {
                    if (preg_match('/^\d{1,2}[\/\-]\d{1,2}[\/\-]\d{4}$/', $date)) {
                        $parts = preg_split('/[\/\-]/', $date);
                        $day = (int) $parts[0];
                    } elseif (preg_match('/^\d{4}[\/\-]\d{1,2}[\/\-]\d{1,2}$/', $date)) {
                        $parts = preg_split('/[\/\-]/', $date);
                        $day = (int) $parts[2];
                    }
                }
            }
        }

        if ($day === null) return;

        if ($day >= 1 && $day <= 7) {
            $week = 1;
        } elseif ($day >= 8 && $day <= 14) {
            $week = 2;
        } elseif ($day >= 15 && $day <= 21) {
            $week = 3;
        } elseif ($day >= 22 && $day <= 28) {
            $week = 4;
        } else {
            $week = 5;
        }

        $set('minggu_ke', $week);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('cabang')
                    ->label('Cabang')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('minggu_ke')
                    ->label('Minggu Ke')
                    ->formatStateUsing(fn ($state) => "Minggu {$state}")
                    ->sortable(),
                TextColumn::make('nominal')
                    ->label('Nominal')
                    ->money('idr')
                    ->sortable(),
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSetoranMingguan::route('/'),
        ];
    }
}
