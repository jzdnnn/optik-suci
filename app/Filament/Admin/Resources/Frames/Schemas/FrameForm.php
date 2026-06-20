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
                Section::make('Informasi Frame')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Frame')
                            ->required(),
                        Select::make('frame_category_id')
                            ->relationship('frameCategory', 'name')
                            ->label('Kategori Frame')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Nama Kategori')
                                    ->required(),
                            ]),
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
                    ])->columns(['sm' => 1, 'md' => 2, 'lg' => 3, 'xl' => 4])
                    ->columnSpanFull(),
            ]);
    }
}
