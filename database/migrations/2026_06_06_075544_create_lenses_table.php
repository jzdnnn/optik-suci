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
        Schema::create('lenses', function (Blueprint $table) {
            $table->id();
            $table->string('ukuran')->nullable();
            $table->string('jenis_lensa')->nullable();
            $table->string('index_bias')->nullable();
            $table->json('accessories')->nullable();
            $table->string('bahan_lensa')->nullable();
            $table->foreignId('lens_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('jenis_tipe')->nullable();
            $table->integer('total_pasang')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lenses');
    }
};
