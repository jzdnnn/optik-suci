<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cabang_optik', function (Blueprint $table) {
            $table->decimal('saldo_awal', 15, 2)->default(0)->after('alamat')
                  ->comment('Saldo awal sebelum periode pencatatan dimulai');
        });
    }

    public function down(): void
    {
        Schema::table('cabang_optik', function (Blueprint $table) {
            $table->dropColumn('saldo_awal');
        });
    }
};
