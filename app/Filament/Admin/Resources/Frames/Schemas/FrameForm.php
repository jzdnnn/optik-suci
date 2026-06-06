<?php

namespace App\Filament\Admin\Resources\Frames\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;

class FrameForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Umum')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Frame')
                            ->required(),
                        Select::make('frame_category_id')
                            ->relationship('frameCategory', 'name')
                            ->label('Kategori Frame'),
                    ])->columns(2),
                    
                Section::make('Harga & Stok')
                    ->schema([
                        TextInput::make('harga_beli')
                            ->label('Harga Beli')
                            ->prefix('Rp')
                            ->numeric()
                            ->default(0.0),
                        TextInput::make('harga_jual')
                            ->label('Harga Jual')
                            ->prefix('Rp')
                            ->numeric()
                            ->default(0.0),
                    ])->columns(2),
            ]);
    }
}
