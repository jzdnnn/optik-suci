<?php

namespace App\Filament\Admin\Resources\Lenses\Pages;

use App\Filament\Admin\Resources\Lenses\LensResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateLens extends CreateRecord
{
    protected ?string $maxWidth = "full";

    protected static string $resource = LensResource::class;

    public function getMaxContentWidth(): Width | string | null
    {
        return Width::Full;
    }
}
