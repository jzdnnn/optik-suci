<?php

namespace App\Filament\Admin\Resources\Patients\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Radio;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;

class PatientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pasien')
                    ->schema([
                        TextInput::make('nama')
                            ->label('Nama Pasien')
                            ->required(),
                        TextInput::make('no_hp')
                            ->label('No Handphone')
                            ->tel(),
                        Select::make('kategori')
                            ->label('Kategori Pasien')
                            ->options([
                                'Umum' => 'Umum',
                                'BPJS Kelas 1' => 'BPJS Kelas 1',
                                'BPJS Kelas 2' => 'BPJS Kelas 2',
                                'BPJS Kelas 3' => 'BPJS Kelas 3',
                            ]),

                        Radio::make('jenis_kelamin')
                            ->label('Jenis Kelamin')
                            ->options([
                                'Laki-laki' => 'Laki-laki',
                                'Perempuan' => 'Perempuan',
                            ])->inline(),
                        Textarea::make('alamat')
                            ->label('Alamat Lengkap')
                            ->columnSpanFull(),
                    ])->columns(['sm' => 1, 'md' => 2, 'lg' => 3, 'xl' => 4])
                    ->columnSpanFull(),
            ]);
    }
}
