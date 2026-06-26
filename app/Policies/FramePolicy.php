<?php

namespace App\Policies;

use App\Models\Frame;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FramePolicy
{
    use HasPermissionPolicy;

    protected string $resourceName = 'frame';
}
