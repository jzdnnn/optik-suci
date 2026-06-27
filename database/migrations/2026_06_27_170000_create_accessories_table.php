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
        Schema::create('accessories', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->integer('stok')->default(0);
            $table->decimal('harga_beli', 12, 2)->default(0.00);
            $table->decimal('harga_jual', 12, 2)->default(0.00);
            $table->timestamps();
        });

        Schema::create('barang_keluar_accessories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_keluar_id')->constrained('barang_keluar')->cascadeOnDelete();
            $table->foreignId('accessory_id')->constrained('accessories')->cascadeOnDelete();
            $table->integer('qty')->default(1);
            $table->decimal('harga_jual_satuan', 12, 2)->default(0.00);
            $table->decimal('subtotal_jual', 12, 2)->default(0.00);
            $table->decimal('harga_beli_satuan', 12, 2)->default(0.00);
            $table->decimal('subtotal_beli', 12, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_keluar_accessories');
        Schema::dropIfExists('accessories');
    }
};
