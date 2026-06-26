<?php

namespace App\Policies;

use App\Models\User;

class LensOwnershipCategoryPolicy
{
    use HasPermissionPolicy;

    protected string $resourceName = 'lens_ownership_category';
}
