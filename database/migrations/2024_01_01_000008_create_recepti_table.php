<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recepti', function (Blueprint $table) {
            $table->id();
            $table->string('broj_recepta')->unique();
            $table->date('datum_izdavanja');
            $table->date('datum_vazenja');
            $table->string('dijagnoza_sifra')->nullable();
            $table->enum('status', ['izdat', 'realizovan', 'istekao'])->default('izdat');
            $table->text('napomena')->nullable();
            $table->string('ime_pacijenta')->nullable();
            $table->string('jmbg_pacijenta', 13);
            $table->string('id_lekara');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recepti');
    }
};
