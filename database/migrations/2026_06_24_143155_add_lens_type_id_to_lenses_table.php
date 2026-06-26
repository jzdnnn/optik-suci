<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lenses', function (Blueprint $table) {
            $table->foreignId('lens_type_id')->nullable()->after('id')->constrained('lens_types')->nullOnDelete();
        });

        $lenses = DB::table('lenses')->get();
        foreach ($lenses as $lens) {
            if (isset($lens->jenis_lensa) && $lens->jenis_lensa) {
                $lensTypeId = DB::table('lens_types')->where('name', $lens->jenis_lensa)->value('id');
                if (!$lensTypeId) {
                    $lensTypeId = DB::table('lens_types')->insertGetId([
                        'name' => $lens->jenis_lensa,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                DB::table('lenses')->where('id', $lens->id)->update(['lens_type_id' => $lensTypeId]);
            }
        }

        Schema::table('lenses', function (Blueprint $table) {
            $table->dropColumn('jenis_lensa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lenses', function (Blueprint $table) {
            $table->string('jenis_lensa')->nullable();
        });

        $lenses = DB::table('lenses')
            ->join('lens_types', 'lenses.lens_type_id', '=', 'lens_types.id')
            ->select('lenses.id', 'lens_types.name')
            ->get();

        foreach ($lenses as $lens) {
            DB::table('lenses')->where('id', $lens->id)->update(['jenis_lensa' => $lens->name]);
        }

        Schema::table('lenses', function (Blueprint $table) {
            $table->dropForeign(['lens_type_id']);
            $table->dropColumn('lens_type_id');
        });
    }
};
