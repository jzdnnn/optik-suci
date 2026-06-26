<?php

namespace App\Policies;

class LaporanBulananPolicy
{
    use HasPermissionPolicy;

    protected string $resourceName = 'laporan_bulanan';
}
