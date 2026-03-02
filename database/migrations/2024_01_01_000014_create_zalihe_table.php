<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zalihe', function (Blueprint $table) {
            $table->foreignId('apoteka_id')->constrained('apoteke')->cascadeOnDelete();
            $table->foreignId('lek_id')->constrained('lekovi')->cascadeOnDelete();
            $table->integer('kolicina')->unsigned()->default(0);
            $table->decimal('prodajna_cena', 10, 2);
            $table->integer('min_zaliha')->unsigned()->default(10);
            $table->timestamp('datum_azuriranja')->useCurrent();
            $table->primary(['apoteka_id', 'lek_id']);
        });

        DB::statement('ALTER TABLE zalihe ADD CONSTRAINT chk_zalihe_kolicina CHECK (kolicina >= 0)');
        DB::statement('ALTER TABLE zalihe ADD CONSTRAINT chk_zalihe_prodajna_cena CHECK (prodajna_cena > 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('zalihe');
    }
};
