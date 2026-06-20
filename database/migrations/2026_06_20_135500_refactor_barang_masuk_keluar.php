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
        // 1. Rename tables
        Schema::rename('inventories', 'barang_masuks');
        Schema::rename('transactions', 'barang_keluars');
        Schema::rename('inventory_histories', 'riwayat_barang_masuks');

        // 2. Rename columns in barang_masuks (morphs)
        Schema::table('barang_masuks', function (Blueprint $table) {
            $table->renameColumn('inventoryable_type', 'barang_masukable_type');
            $table->renameColumn('inventoryable_id', 'barang_masukable_id');
        });

        // 3. Rename columns in riwayat_barang_masuks (foreign key)
        Schema::table('riwayat_barang_masuks', function (Blueprint $table) {
            $table->renameColumn('inventory_id', 'barang_masuk_id');
        });
    }

    public function down(): void
    {
        // Reverse everything
        Schema::table('riwayat_barang_masuks', function (Blueprint $table) {
            $table->renameColumn('barang_masuk_id', 'inventory_id');
        });

        Schema::table('barang_masuks', function (Blueprint $table) {
            $table->renameColumn('barang_masukable_type', 'inventoryable_type');
            $table->renameColumn('barang_masukable_id', 'inventoryable_id');
        });

        Schema::rename('riwayat_barang_masuks', 'inventory_histories');
        Schema::rename('barang_keluars', 'transactions');
        Schema::rename('barang_masuks', 'inventories');
    }
};
