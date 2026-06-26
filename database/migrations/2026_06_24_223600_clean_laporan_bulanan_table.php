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
        Schema::table('laporan_bulanan', function (Blueprint $table) {
            $table->dropColumn([
                'uang_makan',
                'bonus_frame',
                'bpjs_karyawan',
                'speedy_internet',
                'listrik',
                'jne',
                'faset_finish',
                'faset_habis',
                'keperluan_lain',
                'total_frame',
                'domba_mas',
                'essilor',
                'polycore',
                'lensa_stok',
                'lap_cepuk',
                'gaji_pegawai',
                'sewa_tempat',
                'pajak',
                'alat_alat',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_bulanan', function (Blueprint $table) {
            // D. Pengeluaran Operasional Harian
            $table->decimal('uang_makan', 15, 2)->default(0);
            $table->decimal('bonus_frame', 15, 2)->default(0);
            $table->decimal('bpjs_karyawan', 15, 2)->default(0);
            $table->decimal('speedy_internet', 15, 2)->default(0);
            $table->decimal('listrik', 15, 2)->default(0);
            $table->decimal('jne', 15, 2)->default(0);
            $table->decimal('faset_finish', 15, 2)->default(0);
            $table->decimal('faset_habis', 15, 2)->default(0);
            $table->decimal('keperluan_lain', 15, 2)->default(0);

            // E. Pengeluaran Stok dan Persediaan
            $table->decimal('total_frame', 15, 2)->default(0);
            $table->decimal('domba_mas', 15, 2)->default(0);
            $table->decimal('essilor', 15, 2)->default(0);
            $table->decimal('polycore', 15, 2)->default(0);
            $table->decimal('lensa_stok', 15, 2)->default(0);
            $table->decimal('lap_cepuk', 15, 2)->default(0);

            // F. Pengeluaran Gaji dan Lainnya
            $table->decimal('gaji_pegawai', 15, 2)->default(0);
            $table->decimal('sewa_tempat', 15, 2)->default(0);
            $table->decimal('pajak', 15, 2)->default(0);
            $table->decimal('alat_alat', 15, 2)->default(0);
        });
    }
};
