<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('korisnici', function (Blueprint $table) {
            $table->id();
            $table->string('ime');
            $table->string('prezime');
            $table->string('email')->unique();
            $table->string('lozinka_hash');
            $table->boolean('aktivan')->default(true);
            $table->timestamp('datum_kreiranja')->useCurrent();
            $table->timestamp('poslednja_prijava')->nullable();
            $table->foreignId('apoteka_id')->nullable()->constrained('apoteke')->nullOnDelete();
            $table->enum('tip', ['F', 'A', 'C', 'R'])->comment('F=Farmaceut, A=Admin apoteke, C=Centralni admin, R=Registrovani korisnik');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('korisnici');
    }
};
