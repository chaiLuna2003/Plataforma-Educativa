<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modulos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('curso_id')
                ->constrained('cursos')
                ->cascadeOnDelete();

            $table->string('titulo');
            $table->text('descripcion')->nullable();

            $table->unsignedInteger('orden')->default(0);
            $table->string('estado')->default('borrador');

            $table->timestamps();

            $table->index(['curso_id', 'estado', 'orden']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modulos');
    }
};
