<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stavke_prodaje', function (Blueprint $table) {
            $table->foreignId('prodaja_id')->constrained('prodaje')->cascadeOnDelete();
            $table->unsignedInteger('redni_broj');
            $table->foreignId('lek_id')->constrained('lekovi')->restrictOnDelete();
            $table->integer('kolicina')->unsigned();
            $table->decimal('cena_po_komadu', 10, 2);
            $table->decimal('popust', 10, 2)->default(0);
            $table->primary(['prodaja_id', 'redni_broj']);
        });

        DB::statement('ALTER TABLE stavke_prodaje ADD CONSTRAINT chk_stavke_prodaje_kolicina CHECK (kolicina > 0)');
        DB::statement('ALTER TABLE stavke_prodaje ADD CONSTRAINT chk_stavke_prodaje_cena CHECK (cena_po_komadu > 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('stavke_prodaje');
    }
};
