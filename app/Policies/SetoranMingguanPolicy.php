<?php

namespace App\Policies;

class SetoranMingguanPolicy
{
    use HasPermissionPolicy;

    protected string $resourceName = 'setoran_mingguan';
}
