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
                Grid::make(2)->schema([
                    Section::make('Data Diri')
                        ->schema([
                            TextInput::make('nama')
                                ->label('Nama Pasien')
                                ->required(),
                            Radio::make('jenis_kelamin')
                                ->label('Jenis Kelamin')
                                ->options([
                                    'Laki-laki' => 'Laki-laki',
                                    'Perempuan' => 'Perempuan',
                                ])->inline(),
                            TextInput::make('no_hp')
                                ->label('No Handphone')
                                ->tel(),
                            Textarea::make('alamat')
                                ->label('Alamat Lengkap')
                                ->columnSpanFull(),
                        ])->columnSpan(1),
                        
                    Section::make('Data Transaksi')
                        ->schema([
                            Select::make('kategori')
                                ->label('Kategori Pasien')
                                ->options([
                                    'Umum' => 'Umum',
                                    'BPJS Kelas 1' => 'BPJS Kelas 1',
                                    'BPJS Kelas 2' => 'BPJS Kelas 2',
                                    'BPJS Kelas 3' => 'BPJS Kelas 3',
                                ]),
                            TextInput::make('no_bon')
                                ->label('No BON'),
                            DatePicker::make('tanggal_pengambilan')
                                ->label('Tanggal Pengambilan')
                                ->native(false),
                        ])->columnSpan(1),
                ])
            ]);
    }
}
