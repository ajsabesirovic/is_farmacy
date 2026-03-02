<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('narudzbenice', function (Blueprint $table) {
            $table->id();
            $table->string('broj_narudzbenice')->unique();
            $table->timestamp('datum_kreiranja')->useCurrent();
            $table->date('datum_isporuke')->nullable();
            $table->enum('status', ['nacrt', 'poslata', 'isporucena', 'otkazana'])->default('nacrt');
            $table->text('napomena')->nullable();
            $table->foreignId('apoteka_id')->constrained('apoteke')->restrictOnDelete();
            $table->foreignId('dobavljac_id')->constrained('dobavljaci')->restrictOnDelete();
            $table->foreignId('korisnik_id')->constrained('korisnici')->restrictOnDelete();
            $table->decimal('ukupna_vrednost', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('narudzbenice');
    }
};
