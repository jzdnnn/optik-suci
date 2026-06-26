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
            ['name' => 'Domas', 'type' => 'Stok Optik'],
            ['name' => 'Poly', 'type' => 'Stok Optik'],
            ['name' => 'Essilor', 'type' => 'Stok Optik'],
            ['name' => 'Ayi', 'type' => 'Luar Optik'],
            ['name' => 'Hasbi', 'type' => 'Luar Optik'],
        ];
        foreach ($lensCategories as $cat) {
            \App\Models\LensOwnershipCategory::firstOrCreate(
                ['name' => $cat['name']],
                ['type' => $cat['type']]
            );
        }

        $lensTypes = ['Single Vision', 'Kryptok', 'Flat top', 'Progressive', 'Poly Carbonate'];
        foreach ($lensTypes as $type) {
            \App\Models\LensType::firstOrCreate(['name' => $type]);
        }
    }
}
