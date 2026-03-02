<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dobavljaci', function (Blueprint $table) {
            $table->id();
            $table->string('naziv');
            $table->string('pib')->unique();
            $table->string('telefon', 20)->nullable();
            $table->string('email')->nullable();
            $table->boolean('aktivan')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dobavljaci');
    }
};
