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
        Schema::create('laporan_bulanan', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('bulan');
            $table->unsignedSmallInteger('tahun');
            $table->string('cabang');

            // A. Pendapatan
            $table->decimal('omzet', 15, 2)->default(0);
            $table->decimal('pendapatan_bpjs', 15, 2)->default(0);
            $table->decimal('pendapatan_harian', 15, 2)->default(0);

            // B. Rincian Selisih (Stored as JSON array of objects)
            $table->json('selisih_details')->nullable();

            // C. Setoran Mingguan
            $table->decimal('setoran_minggu_1', 15, 2)->default(0);
            $table->decimal('setoran_minggu_2', 15, 2)->default(0);
            $table->decimal('setoran_minggu_3', 15, 2)->default(0);
            $table->decimal('setoran_minggu_4', 15, 2)->default(0);
            $table->decimal('setoran_minggu_5', 15, 2)->default(0);

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

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_bulanan');
    }
};
