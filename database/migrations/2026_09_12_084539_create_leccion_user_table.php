<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leccion_user', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('leccion_id')
                ->constrained('lecciones')
                ->cascadeOnDelete();

            $table->timestamp('completed_at');
            $table->timestamps();

            $table->unique(['user_id', 'leccion_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leccion_user');
    }
};
