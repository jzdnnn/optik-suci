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
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'ukuran_kanan_sph',
                'ukuran_kanan_cyl',
                'ukuran_kanan_axis',
                'ukuran_kiri_sph',
                'ukuran_kiri_cyl',
                'ukuran_kiri_axis',
            ]);
            
            $table->integer('jumlah_lensa_pcs')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('ukuran_kanan_sph')->nullable();
            $table->string('ukuran_kanan_cyl')->nullable();
            $table->string('ukuran_kanan_axis')->nullable();
            $table->string('ukuran_kiri_sph')->nullable();
            $table->string('ukuran_kiri_cyl')->nullable();
            $table->string('ukuran_kiri_axis')->nullable();
            
            $table->dropColumn('jumlah_lensa_pcs');
        });
    }
};
