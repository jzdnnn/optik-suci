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

        $lensCategories = ['Stok Optik', 'Luar Optik'];
        foreach ($lensCategories as $category) {
            \App\Models\LensOwnershipCategory::firstOrCreate(['name' => $category]);
        }
    }
}
