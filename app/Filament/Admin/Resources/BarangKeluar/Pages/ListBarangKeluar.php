<?php

namespace App\Filament\Admin\Resources\BarangKeluar\Pages;

use App\Filament\Admin\Resources\BarangKeluar\BarangKeluarResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBarangKeluar extends ListRecords
{
    protected static string $resource = BarangKeluarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
