<?php

namespace App\Filament\Admin\Resources\FrameCategories\Pages;

use App\Filament\Admin\Resources\FrameCategories\FrameCategoryResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateFrameCategory extends CreateRecord
{
    protected ?string $maxWidth = "full";

    protected static string $resource = FrameCategoryResource::class;

    public function getMaxContentWidth(): Width | string | null
    {
        return Width::Full;
    }
}
