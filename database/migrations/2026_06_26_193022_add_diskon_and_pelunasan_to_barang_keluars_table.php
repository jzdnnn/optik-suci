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
            $table->decimal('diskon', 15, 2)->default(0)->nullable();
            $table->decimal('potongan_bpjs', 15, 2)->default(0)->nullable();
            $table->date('tanggal_pelunasan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->dropColumn(['diskon', 'potongan_bpjs', 'tanggal_pelunasan']);
        });
    }
};
