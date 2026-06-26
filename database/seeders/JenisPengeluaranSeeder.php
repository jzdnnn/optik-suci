<?php

namespace Database\Seeders;

use App\Models\JenisPengeluaran;
use Illuminate\Database\Seeder;

class JenisPengeluaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Operasional
            ['nama' => 'Uang Makan', 'tipe' => 'operasional'],
            ['nama' => 'Bonus Frame', 'tipe' => 'operasional'],
            ['nama' => 'BPJS (Asuransi)', 'tipe' => 'operasional'],
            ['nama' => 'Speedy / Internet', 'tipe' => 'operasional'],
            ['nama' => 'Listrik', 'tipe' => 'operasional'],
            ['nama' => 'JNE', 'tipe' => 'operasional'],
            ['nama' => 'Faset Finish', 'tipe' => 'operasional'],
            ['nama' => 'Faset Habis/Hasbi', 'tipe' => 'operasional'],
            ['nama' => 'Keperluan Lain-lain', 'tipe' => 'operasional'],

            // Stok
            ['nama' => 'Total Frame', 'tipe' => 'stok'],
            ['nama' => 'Domba Mas', 'tipe' => 'stok'],
            ['nama' => 'Essilor', 'tipe' => 'stok'],
            ['nama' => 'Polycore', 'tipe' => 'stok'],
            ['nama' => 'Lensa Stok', 'tipe' => 'stok'],
            ['nama' => 'Lap Cepuk', 'tipe' => 'stok'],

            // Gaji & Lainnya
            ['nama' => 'Gaji Pegawai', 'tipe' => 'gaji'],
            ['nama' => 'Sewa Tempat', 'tipe' => 'gaji'],
            ['nama' => 'Pajak', 'tipe' => 'gaji'],
            ['nama' => 'Alat-Alat', 'tipe' => 'gaji'],
        ];

        foreach ($categories as $category) {
            JenisPengeluaran::updateOrCreate(
                ['nama' => $category['nama']],
                ['tipe' => $category['tipe']]
            );
        }
    }
}
