<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recept_lek', function (Blueprint $table) {
            $table->foreignId('recept_id')->constrained('recepti')->cascadeOnDelete();
            $table->foreignId('lek_id')->constrained('lekovi')->restrictOnDelete();
            $table->integer('kolicina')->unsigned();
            $table->string('doziranje')->nullable();
            $table->primary(['recept_id', 'lek_id']);
        });

        DB::statement('ALTER TABLE recept_lek ADD CONSTRAINT chk_recept_lek_kolicina CHECK (kolicina > 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('recept_lek');
    }
};
