<?php

namespace App\Filament\Admin\Resources\LaporanBulanan\Pages;

use App\Filament\Admin\Resources\LaporanBulanan\LaporanBulananResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLaporanBulanan extends ListRecords
{
    protected static string $resource = LaporanBulananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
