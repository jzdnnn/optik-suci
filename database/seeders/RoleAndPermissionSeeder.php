<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Define features/resources
        $resources = [
            'user',
            'role',
            'frame_category',
            'frame',
            'lens_ownership_category',
            'lens_type',
            'lens',
            'patient',
            'barang_masuk',
            'barang_keluar',
            'laporan_keuangan',
            'laporan_bulanan',
            'jenis_pengeluaran',
            'pengeluaran',
            'setoran_mingguan',
        ];

        // Define actions
        $actions = ['viewAny', 'view', 'create', 'update', 'delete'];

        // Create permissions
        foreach ($resources as $resource) {
            foreach ($actions as $action) {
                Permission::findOrCreate("{$action}_{$resource}");
            }
        }

        // Create Super Admin role and assign all permissions
        $superAdminRole = Role::findOrCreate('super_admin');
        $superAdminRole->givePermissionTo(Permission::all());

        // Create Admin Optik Suci if it exists and assign super_admin
        $admin = User::where('email', 'admin@optiksuci.com')->first();
        if ($admin) {
            $admin->assignRole($superAdminRole);
        }
    }
}
