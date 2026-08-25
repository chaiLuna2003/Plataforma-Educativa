<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 30)
                ->default('estudiante')
                ->after('password');

            $table->boolean('is_active')
                ->default(true)
                ->after('role');

            $table->timestamp('invited_at')
                ->nullable()
                ->after('is_active');

            $table->foreignId('invited_by')
                ->nullable()
                ->after('invited_at')
                ->constrained('users')
                ->nullOnDelete();

            $table->index(['role', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['invited_by']);
            $table->dropIndex(['role', 'is_active']);

            $table->dropColumn([
                'role',
                'is_active',
                'invited_at',
                'invited_by',
            ]);
        });
    }
};
