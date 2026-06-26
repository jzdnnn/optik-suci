<?php

namespace App\Filament\Admin\Resources\SetoranMingguan\Pages;

use App\Filament\Admin\Resources\SetoranMingguan\SetoranMingguanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageSetoranMingguan extends ManageRecords
{
    protected static string $resource = SetoranMingguanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
