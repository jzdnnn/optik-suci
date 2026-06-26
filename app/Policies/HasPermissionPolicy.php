<?php

namespace App\Policies;

use App\Models\User;

trait HasPermissionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo("viewAny_{$this->resourceName}");
    }

    public function view(User $user, $model = null): bool
    {
        return $user->hasPermissionTo("view_{$this->resourceName}");
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo("create_{$this->resourceName}");
    }

    public function update(User $user, $model = null): bool
    {
        return $user->hasPermissionTo("update_{$this->resourceName}");
    }

    public function delete(User $user, $model = null): bool
    {
        return $user->hasPermissionTo("delete_{$this->resourceName}");
    }
}
