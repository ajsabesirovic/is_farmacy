<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lekovi', function (Blueprint $table) {
            $table->id();
            $table->string('naziv');
            $table->string('proizvodjac')->nullable();
            $table->string('jkl_sifra')->unique();
            $table->string('farm_oblik')->nullable();
            $table->string('jacina')->nullable();
            $table->string('pakovanje')->nullable();
            $table->boolean('na_recept')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lekovi');
    }
};
