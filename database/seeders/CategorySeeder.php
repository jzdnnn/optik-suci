<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $frameCategories = ['Iware', 'Sunglasses', 'Kacamata Baca'];
        foreach ($frameCategories as $category) {
            \App\Models\FrameCategory::firstOrCreate(['name' => $category]);
        }

        $lensCategories = [
            ['name' => 'Domas + Faset', 'type' => 'Stok Optik'],
            ['name' => 'Poly + Faset', 'type' => 'Stok Optik'],
            ['name' => 'Essilor + Faset', 'type' => 'Stok Optik'],
            ['name' => 'Ayi + Faset', 'type' => 'Luar Optik'],
            ['name' => 'Hasbi + Faset', 'type' => 'Luar Optik'],
            ['name' => 'Oriental + Faset', 'type' => 'Stok Optik'],
        ];
        foreach ($lensCategories as $cat) {
            \App\Models\LensOwnershipCategory::firstOrCreate(
                ['name' => $cat['name']],
                ['type' => $cat['type']]
            );
        }

        $lensTypes = ['Blu Ray', 'Photochromic', 'Night Vision', 'Blue Chromic', 'Warna', 'Standar'];
        foreach ($lensTypes as $type) {
            \App\Models\LensType::firstOrCreate(['name' => $type]);
        }
    }
}
