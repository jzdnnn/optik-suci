<?php

namespace App\Policies;

use App\Models\FrameCategory;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FrameCategoryPolicy
{
    use HasPermissionPolicy;

    protected string $resourceName = 'frame_category';
}
