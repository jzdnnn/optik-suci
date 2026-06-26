<?php

namespace App\Policies;

use App\Models\User;

class LensTypePolicy
{
    use HasPermissionPolicy;

    protected string $resourceName = 'lens_type';
}
