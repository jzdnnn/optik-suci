<?php

namespace App\Policies;

use App\Models\User;

class BarangMasukPolicy
{
    use HasPermissionPolicy;

    protected string $resourceName = 'barang_masuk';
}
