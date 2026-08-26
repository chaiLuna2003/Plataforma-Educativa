<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lecciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('modulo_id')
                ->constrained('modulos')
                ->cascadeOnDelete();

            $table->string('titulo');
            $table->text('descripcion')->nullable();

            $table->string('tipo')->default('video');
            $table->longText('contenido')->nullable();
            $table->string('archivo_path')->nullable();

            // Información del video alojado en Vimeo
            $table->string('vimeo_video_id')->nullable();
            $table->string('vimeo_video_hash')->nullable();
            $table->string('miniatura_url')->nullable();
            $table->unsignedInteger('duracion_segundos')->nullable();

            $table->boolean('es_muestra')->default(false);
            $table->unsignedInteger('orden')->default(0);
            $table->string('estado')->default('borrador');
            $table->timestamp('publicado_at')->nullable();

            $table->timestamps();

            $table->index(['modulo_id', 'estado', 'orden']);
            $table->index('vimeo_video_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lecciones');
    }
};