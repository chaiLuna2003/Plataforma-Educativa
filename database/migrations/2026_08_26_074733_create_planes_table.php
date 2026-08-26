<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planes', function (Blueprint $table) {
            $table->id();

            $table->string('nombre');
            $table->string('slug')->unique();
            $table->text('descripcion')->nullable();

            $table->string('estado')->default('activo');
            $table->unsignedInteger('orden')->default(0);

            $table->timestamps();

            $table->index(['estado', 'orden']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planes');
    }
};