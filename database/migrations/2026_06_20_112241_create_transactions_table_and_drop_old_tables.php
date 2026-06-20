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
        Schema::dropIfExists('frame_transactions');
        Schema::dropIfExists('lens_transactions');

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            
            $table->enum('tipe_transaksi', ['frame', 'lensa', 'lengkap']);
            
            $table->foreignId('frame_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('lens_id')->nullable()->constrained()->nullOnDelete();
            
            $table->decimal('harga_frame', 15, 2)->nullable();
            $table->decimal('harga_lensa', 15, 2)->nullable();
            $table->decimal('total_transaksi', 15, 2)->default(0);
            
            $table->enum('status_pembayaran', ['lunas', 'dp', 'belum_bayar'])->default('belum_bayar');
            $table->decimal('dp_dibayar', 15, 2)->nullable();
            $table->decimal('diskon', 15, 2)->nullable();
            
            $table->string('ukuran_kanan_sph')->nullable();
            $table->string('ukuran_kanan_cyl')->nullable();
            $table->string('ukuran_kanan_axis')->nullable();
            
            $table->string('ukuran_kiri_sph')->nullable();
            $table->string('ukuran_kiri_cyl')->nullable();
            $table->string('ukuran_kiri_axis')->nullable();
            
            $table->string('index_bias')->nullable();
            $table->json('aksesoris')->nullable();
            
            $table->date('tanggal_transaksi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
