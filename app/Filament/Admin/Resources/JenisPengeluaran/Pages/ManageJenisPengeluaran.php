<?php

namespace App\Filament\Admin\Resources\JenisPengeluaran\Pages;

use App\Filament\Admin\Resources\JenisPengeluaran\JenisPengeluaranResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageJenisPengeluaran extends ManageRecords
{
    protected static string $resource = JenisPengeluaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
