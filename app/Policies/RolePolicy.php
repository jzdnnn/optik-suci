<?php

namespace App\Policies;

use App\Models\User;

class RolePolicy
{
    use HasPermissionPolicy;

    protected string $resourceName = 'role';
}
