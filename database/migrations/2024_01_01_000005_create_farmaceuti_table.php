<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farmaceuti', function (Blueprint $table) {
            $table->foreignId('id')->primary()->constrained('korisnici')->cascadeOnDelete();
            $table->string('licenca');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farmaceuti');
    }
};
