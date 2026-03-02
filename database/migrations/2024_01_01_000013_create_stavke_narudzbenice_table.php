<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stavke_narudzbenice', function (Blueprint $table) {
            $table->foreignId('narudzbenica_id')->constrained('narudzbenice')->cascadeOnDelete();
            $table->unsignedInteger('redni_broj');
            $table->foreignId('lek_id')->constrained('lekovi')->restrictOnDelete();
            $table->integer('kolicina')->unsigned();
            $table->decimal('cena_po_komadu', 10, 2);
            $table->primary(['narudzbenica_id', 'redni_broj']);
        });

        DB::statement('ALTER TABLE stavke_narudzbenice ADD CONSTRAINT chk_stavke_nar_kolicina CHECK (kolicina > 0)');
        DB::statement('ALTER TABLE stavke_narudzbenice ADD CONSTRAINT chk_stavke_nar_cena CHECK (cena_po_komadu > 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('stavke_narudzbenice');
    }
};
