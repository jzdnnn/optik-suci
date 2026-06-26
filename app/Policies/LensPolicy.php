<?php

namespace App\Policies;

use App\Models\User;

class LensPolicy
{
    use HasPermissionPolicy;

    protected string $resourceName = 'lens';
}
