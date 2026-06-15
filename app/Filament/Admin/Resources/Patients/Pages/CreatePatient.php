<?php

namespace App\Filament\Admin\Resources\Patients\Pages;

use App\Filament\Admin\Resources\Patients\PatientResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreatePatient extends CreateRecord
{
    protected ?string $maxWidth = "full";

    protected static string $resource = PatientResource::class;

    public function getMaxContentWidth(): Width | string | null
    {
        return Width::Full;
    }
}
