<?php

namespace App\Filament\Admin\Resources\Frames\Pages;

use App\Filament\Admin\Resources\Frames\FrameResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateFrame extends CreateRecord
{
    protected ?string $maxWidth = "full";

    protected static string $resource = FrameResource::class;

    public function getMaxContentWidth(): Width | string | null
    {
        return Width::Full;
    }
}
