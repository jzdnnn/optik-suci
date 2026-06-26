<?php

namespace App\Policies;

class PengeluaranPolicy
{
    use HasPermissionPolicy;

    protected string $resourceName = 'pengeluaran';
}
