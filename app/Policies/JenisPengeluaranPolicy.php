<?php

namespace App\Policies;

class JenisPengeluaranPolicy
{
    use HasPermissionPolicy;

    protected string $resourceName = 'jenis_pengeluaran';
}
