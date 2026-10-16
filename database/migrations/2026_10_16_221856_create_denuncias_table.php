<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('denuncias', function (Blueprint $table) {
            $table->id();
            $table->year('anio');
            $table->unsignedTinyInteger('mes');
            $table->foreignId('distrito_id')->constrained('distritos')->cascadeOnDelete();
            $table->foreignId('modalidad_id')->constrained('modalidades')->cascadeOnDelete();
            $table->unsignedInteger('cantidad');

            $table->unique(['anio', 'mes', 'distrito_id', 'modalidad_id'], 'denuncias_unica');
            $table->index(['anio', 'mes']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('denuncias');
    }
};
