<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dobavljac_lek', function (Blueprint $table) {
            $table->foreignId('dobavljac_id')->constrained('dobavljaci')->cascadeOnDelete();
            $table->foreignId('lek_id')->constrained('lekovi')->cascadeOnDelete();
            $table->decimal('nabavna_cena', 10, 2);
            $table->primary(['dobavljac_id', 'lek_id']);
        });

        DB::statement('ALTER TABLE dobavljac_lek ADD CONSTRAINT chk_dobavljac_lek_cena CHECK (nabavna_cena > 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('dobavljac_lek');
    }
};
