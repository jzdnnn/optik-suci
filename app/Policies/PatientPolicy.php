<?php

namespace App\Policies;

use App\Models\User;

class PatientPolicy
{
    use HasPermissionPolicy;

    protected string $resourceName = 'patient';
}
