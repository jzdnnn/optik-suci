<?php

namespace App\Filament\Admin\Resources\LaporanBulanan\Pages;

use App\Filament\Admin\Resources\LaporanBulanan\LaporanBulananResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLaporanBulanan extends EditRecord
{
    protected static string $resource = LaporanBulananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
