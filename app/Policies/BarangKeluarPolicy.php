<?php

namespace App\Policies;

use App\Models\User;

class BarangKeluarPolicy
{
    use HasPermissionPolicy;

    protected string $resourceName = 'barang_keluar';
}
