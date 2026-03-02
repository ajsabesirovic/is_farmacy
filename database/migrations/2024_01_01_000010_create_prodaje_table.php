<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prodaje', function (Blueprint $table) {
            $table->id();
            $table->date('datum');
            $table->time('vreme');
            $table->enum('nacin_placanja', ['gotovina', 'kartica']);
            $table->foreignId('apoteka_id')->constrained('apoteke')->restrictOnDelete();
            $table->foreignId('korisnik_id')->constrained('korisnici')->restrictOnDelete();
            $table->foreignId('recept_id')->nullable()->unique()->constrained('recepti')->nullOnDelete();
            $table->decimal('ukupan_iznos', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prodaje');
    }
};
