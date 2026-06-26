<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CabangOptikSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua nama cabang unik dari tabel pengeluaran yang sudah ada
        $cabangs = DB::table('pengeluaran')
            ->whereNotNull('cabang')
            ->where('cabang', '!=', '')
            ->distinct()
            ->pluck('cabang');

        // Selalu pastikan OPTIK SUCI ter-seed sebagai cabang default
        DB::table('cabang_optik')->insertOrIgnore([
            'nama'       => 'OPTIK SUCI',
            'alamat'     => null,
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach ($cabangs as $namaCabang) {
            DB::table('cabang_optik')->insertOrIgnore([
                'nama'       => strtoupper(trim($namaCabang)),
                'alamat'     => null,
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
