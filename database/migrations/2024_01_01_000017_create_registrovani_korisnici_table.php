<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrovani_korisnici', function (Blueprint $table) {
            $table->foreignId('id')->primary()->constrained('korisnici')->cascadeOnDelete();
            $table->string('jmbg', 13);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrovani_korisnici');
    }
};
