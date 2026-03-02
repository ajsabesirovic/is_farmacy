<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the old string column if it still exists
        if (Schema::hasColumn('recepti', 'id_lekara')) {
            Schema::table('recepti', function (Blueprint $table) {
                $table->dropColumn('id_lekara');
            });
        }

        // Add lekar_id as nullable first
        if (!Schema::hasColumn('recepti', 'lekar_id')) {
            Schema::table('recepti', function (Blueprint $table) {
                $table->unsignedBigInteger('lekar_id')->nullable()->after('jmbg_pacijenta');
            });
        }

        // Assign first lekar to any existing records
        $firstLekarId = DB::table('lekari')->first()?->id;
        if ($firstLekarId) {
            DB::table('recepti')->whereNull('lekar_id')->update(['lekar_id' => $firstLekarId]);
        }

        // Make lekar_id required and add foreign key
        Schema::table('recepti', function (Blueprint $table) {
            $table->unsignedBigInteger('lekar_id')->nullable(false)->change();
            $table->foreign('lekar_id')->references('id')->on('lekari');
        });
    }

    public function down(): void
    {
        Schema::table('recepti', function (Blueprint $table) {
            $table->dropForeign(['lekar_id']);
            $table->dropColumn('lekar_id');
        });

        Schema::table('recepti', function (Blueprint $table) {
            $table->string('id_lekara')->after('jmbg_pacijenta');
        });
    }
};
