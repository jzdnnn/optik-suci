<?php

namespace Database\Seeders;

use App\Models\BarangKeluar;
use App\Models\BarangMasuk;
use App\Models\Frame;
use App\Models\FrameCategory;
use App\Models\Lens;
use App\Models\LensOwnershipCategory;
use App\Models\LensType;
use App\Models\Patient;
use App\Models\RiwayatBarangMasuk;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure Frame Categories
        $frameCategories = FrameCategory::all();
        if ($frameCategories->isEmpty()) {
            $fcNames = ['Iware', 'Sunglasses', 'Kacamata Baca'];
            foreach ($fcNames as $name) {
                $frameCategories[] = FrameCategory::create(['name' => $name]);
            }
        }

        // 2. Seed Frames & Initial Stock
        $frameNames = [
            'BRENDA', 'LUGANO', 'YUPS', 'NO MERK', 'BERNIE', 'ZABDI', 'MEMPHIS', 'BRIDGE', 'DIAMOND', 'ZABDI M', 'CHAKASU', 'SEIMA', 'BAILEY', 'OSWALD', 'MARC JACOBS', 'CKS',
            'Dior', 'TR 90', 'Titanium', 'Giovani', 'Kelly’s', 'West', 'M', 'Nikon', 'Onpedder', 'Evisu', 'Times', 'Filano', 'Kyoto', 'Lunar', 'Onasis', 'Caraza', 'Passport', 'Esse', 'Diomani', 'Levis', 'Alain delon', 'Trialix', 'Patrice', 'Carthe', 'Mont blank', 'Marc Jacob', 'Bulgarı', 'Dakley', 'Freed', 'Star', 'Mickey', 'HMS', 'Xice', 'SIGG', 'Optica', 'oriental', 'Moe', 'Mozaik', 'Young Kiss', 'Bachelor', 'Guci', 'Miss magda', 'Vandenberg', 'Violinis', 'Live'
        ];
        foreach ($frameNames as $name) {
            $frame = Frame::firstOrCreate(
                ['name' => $name],
                [
                    'frame_category_id' => $frameCategories[array_rand($frameCategories->toArray())]['id'],
                    'harga_beli' => rand(50, 150) * 1000,
                    'harga_jual' => rand(200, 500) * 1000,
                ]
            );

            BarangMasuk::firstOrCreate(
                [
                    'barang_masukable_type' => Frame::class,
                    'barang_masukable_id' => $frame->id,
                ],
                [
                    'stok' => 500,
                    'tanggal_masuk' => now(),
                ]
            );
        }

        // 3. Ensure Lens Ownership & Types from CategorySeeder
        $this->call(CategorySeeder::class);

        $lensTypes = LensType::all()->keyBy('name');
        $lensOwnerships = LensOwnershipCategory::all()->keyBy('name');

        // 4. Seed Specific Lenses requested by user & Initial Stock
        $categoriesList = ['Ayi + Faset', 'Hasbi + Faset', 'Essilor + Faset', 'Domas + Faset', 'Poly + Faset', 'Oriental + Faset'];
        $typesList = ['Standar', 'Blu Ray', 'Photochromic', 'Blue Chromic', 'Night Vision', 'Warna'];
        
        $lensDefinitions = [
            ['name' => 'SV CR MC', 'harga_beli' => 10000, 'harga_jual' => 50000],
            ['name' => 'PROG PROVIEW', 'harga_beli' => 15000, 'harga_jual' => 75000],
            ['name' => 'KT CR MC', 'harga_beli' => 15000, 'harga_jual' => 75000],
            ['name' => 'PROG CR MC', 'harga_beli' => 15000, 'harga_jual' => 75000],
        ];

        foreach ($categoriesList as $catName) {
            $cat = $lensOwnerships->get($catName);
            if (!$cat) continue;

            foreach ($typesList as $typeName) {
                $type = $lensTypes->get($typeName);
                if (!$type) continue;

                $itemsToSeed = ($catName === 'Oriental + Faset') 
                    ? [ ['name' => 'SV CR MC', 'harga_beli' => 10000, 'harga_jual' => 50000] ]
                    : $lensDefinitions;

                foreach ($itemsToSeed as $item) {
                    $lens = Lens::firstOrCreate(
                        [
                            'name' => $item['name'],
                            'lens_type_id' => $type->id,
                            'lens_ownership_category_id' => $cat->id,
                        ],
                        [
                            'bahan_lensa' => 'Plastic',
                            'harga_beli' => $item['harga_beli'],
                            'harga_jual' => $item['harga_jual'],
                        ]
                    );

                    BarangMasuk::firstOrCreate(
                        [
                            'barang_masukable_type' => Lens::class,
                            'barang_masukable_id' => $lens->id,
                        ],
                        [
                            'stok' => 1000,
                            'tanggal_masuk' => now(),
                        ]
                    );
                }
            }
        }
    }
}
