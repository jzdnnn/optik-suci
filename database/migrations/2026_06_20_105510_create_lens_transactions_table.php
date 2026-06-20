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
        Schema::create('lens_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('lens_id')->constrained('lenses')->cascadeOnDelete();
            $table->string('ukuran_kanan_sph')->nullable();
            $table->string('ukuran_kanan_cyl')->nullable();
            $table->string('ukuran_kanan_axis')->nullable();
            $table->string('ukuran_kiri_sph')->nullable();
            $table->string('ukuran_kiri_cyl')->nullable();
            $table->string('ukuran_kiri_axis')->nullable();
            $table->string('index_bias')->nullable();
            $table->json('aksesoris')->nullable();
            $table->decimal('total_pasang', 15, 2)->default(0);
            $table->date('tanggal_keluar');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lens_transactions');
    }
};
