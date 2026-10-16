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
        Schema::create('pronosticos', function (Blueprint $table) {
            $table->id();
            $table->string('serie');
            $table->unsignedSmallInteger('anio');
            $table->unsignedTinyInteger('mes');
            $table->unsignedInteger('valor');
            $table->unsignedInteger('limite_inferior')->nullable();
            $table->unsignedInteger('limite_superior')->nullable();
            $table->timestamps();

            $table->unique(['serie', 'anio', 'mes']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pronosticos');
    }
};
