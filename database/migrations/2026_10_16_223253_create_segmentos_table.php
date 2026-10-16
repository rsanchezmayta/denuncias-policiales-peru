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
        Schema::create('segmentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provincia_id')->unique()->constrained('provincias')->cascadeOnDelete();
            $table->unsignedTinyInteger('cluster');
            $table->unsignedInteger('total');
            $table->decimal('crecimiento', 6, 2);
            $table->string('modalidad_dominante');
            $table->decimal('pct_nacional', 6, 4);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('segmentos');
    }
};
