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
        Schema::table('lenses', function (Blueprint $table) {
            if (Schema::hasColumn('lenses', 'lens_category_id')) {
                // Ignore error if foreign key doesn't exist
                try { $table->dropForeign(['lens_category_id']); } catch (\Exception $e) {}
                $table->dropColumn('lens_category_id');
            }
            if (Schema::hasColumn('lenses', 'ukuran')) {
                $table->dropColumn(['ukuran', 'index_bias', 'accessories', 'total_pasang', 'jenis_tipe']);
            }
            if (!Schema::hasColumn('lenses', 'name')) {
                $table->string('name')->after('id');
            }
            if (!Schema::hasColumn('lenses', 'lens_ownership_category_id')) {
                $table->foreignId('lens_ownership_category_id')->nullable()->constrained('lens_ownership_categories')->nullOnDelete();
            }
            if (!Schema::hasColumn('lenses', 'harga_beli')) {
                $table->decimal('harga_beli', 15, 2)->default(0);
                $table->decimal('harga_jual', 15, 2)->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lenses', function (Blueprint $table) {
            $table->dropForeign(['lens_ownership_category_id']);
            $table->dropColumn(['name', 'lens_ownership_category_id', 'harga_beli', 'harga_jual']);
            
            $table->string('ukuran')->nullable();
            $table->string('index_bias')->nullable();
            $table->json('accessories')->nullable();
            $table->integer('total_pasang')->default(0);
            $table->string('jenis_tipe')->nullable();
            $table->foreignId('lens_category_id')->nullable()->constrained('lens_categories')->nullOnDelete();
        });
    }
};
