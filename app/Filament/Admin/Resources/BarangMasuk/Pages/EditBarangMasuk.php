<?php

namespace App\Filament\Admin\Resources\BarangMasuk\Pages;

use App\Filament\Admin\Resources\BarangMasuk\BarangMasukResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBarangMasuk extends EditRecord
{
    protected static string $resource = BarangMasukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
