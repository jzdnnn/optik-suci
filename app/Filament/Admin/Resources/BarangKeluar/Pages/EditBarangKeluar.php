<?php

namespace App\Filament\Admin\Resources\BarangKeluar\Pages;

use App\Filament\Admin\Resources\BarangKeluar\BarangKeluarResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBarangKeluar extends EditRecord
{
    protected static string $resource = BarangKeluarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
