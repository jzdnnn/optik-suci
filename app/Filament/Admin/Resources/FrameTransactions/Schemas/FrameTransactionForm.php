<?php

namespace App\Filament\Admin\Resources\FrameTransactions\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Section;
use App\Models\Frame;

class FrameTransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make("Informasi Transaksi Frame")
                    ->schema([
                        Select::make("patient_id")
                            ->relationship("patient", "nama")
                            ->label("Nama Pasien")
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make("frame_id")
                            ->relationship("frame", "name")
                            ->label("Frame")
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set("harga", Frame::find($state)?->harga_jual ?? 0)),
                        TextInput::make("harga")
                            ->label("Harga")
                            ->numeric()
                            ->prefix("Rp")
                            ->required()
                            ->default(0),
                        DatePicker::make("tanggal_keluar")
                            ->label("Tanggal Keluar")
                            ->native(false)
                            ->default(now())
                            ->required(),
                    ])->columns(["sm" => 1, "md" => 2, "lg" => 3, "xl" => 4])
                    ->columnSpanFull(),
            ]);
    }
}
