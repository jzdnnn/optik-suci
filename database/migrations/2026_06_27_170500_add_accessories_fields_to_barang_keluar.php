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
        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->decimal('total_aksesoris', 12, 2)->default(0.00)->after('tambahan_biaya');
            $table->decimal('biaya_beli_aksesoris', 12, 2)->default(0.00)->after('total_aksesoris');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->dropColumn(['total_aksesoris', 'biaya_beli_aksesoris']);
        });
    }
};
