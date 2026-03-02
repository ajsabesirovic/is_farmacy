<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recept_lek', function (Blueprint $table) {
            // Količina koju je farmaceut izdao (može biti manja ili jednaka propisanoj)
            $table->integer('izdata_kolicina')->unsigned()->default(0)->after('kolicina');
        });
    }

    public function down(): void
    {
        Schema::table('recept_lek', function (Blueprint $table) {
            $table->dropColumn('izdata_kolicina');
        });
    }
};
