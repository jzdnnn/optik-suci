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
        Schema::create('setoran_mingguan', function (Blueprint $table) {
            $table->id();
            $table->string('cabang');
            $table->date('tanggal');
            $table->unsignedTinyInteger('minggu_ke'); // 1, 2, 3, 4, 5
            $table->decimal('nominal', 15, 2)->default(0);
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setoran_mingguan');
    }
};
