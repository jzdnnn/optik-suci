<?php

namespace App\Filament\Admin\Resources\Frames\Pages;

use App\Filament\Admin\Resources\Frames\FrameResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFrame extends EditRecord
{
    protected static string $resource = FrameResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
