<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::rename('barang_masuks', 'barang_masuk');
        Schema::rename('barang_keluars', 'barang_keluar');
        Schema::rename('riwayat_barang_masuks', 'riwayat_barang_masuk');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('riwayat_barang_masuk', 'riwayat_barang_masuks');
        Schema::rename('barang_keluar', 'barang_keluars');
        Schema::rename('barang_masuk', 'barang_masuks');
    }
};
