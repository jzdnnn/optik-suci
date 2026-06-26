<?php

namespace App\Filament\Admin\Resources\Pengeluaran\Pages;

use App\Filament\Admin\Resources\Pengeluaran\PengeluaranResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManagePengeluaran extends ManageRecords
{
    protected static string $resource = PengeluaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
