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
        Schema::create('jenis_pengeluaran', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->enum('tipe', ['operasional', 'stok', 'gaji']);
            $table->timestamps();
        });

        Schema::create('pengeluaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_pengeluaran_id')->constrained('jenis_pengeluaran')->cascadeOnDelete();
            $table->string('cabang');
            $table->date('tanggal');
            $table->decimal('nominal', 15, 2);
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengeluaran');
        Schema::dropIfExists('jenis_pengeluaran');
    }
};
