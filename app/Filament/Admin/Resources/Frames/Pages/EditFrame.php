<?php

namespace App\Filament\Admin\Resources\Frames\Pages;

use App\Filament\Admin\Resources\Frames\FrameResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;

class EditFrame extends EditRecord
{
    protected ?string $maxWidth = "full";

    protected static string $resource = FrameResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    public function getMaxContentWidth(): Width | string | null
    {
        return Width::Full;
    }
}
