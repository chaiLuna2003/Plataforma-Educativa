<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Curso extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'slug',
        'descripcion',
        'imagen_path',
        'nivel',
        'estado',
        'orden',
        'publicado_at',
        'creado_por',
    ];

    protected function casts(): array
    {
        return [
            'orden' => 'integer',
            'publicado_at' => 'datetime',
        ];
    }

    public function modulos(): HasMany
    {
        return $this->hasMany(Modulo::class)
            ->orderBy('orden')
            ->orderBy('id');
    }

    public function planes(): BelongsToMany
{
    return $this->belongsToMany(Plan::class, 'curso_plan')
        ->withTimestamps();
}

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function estaPublicado(): bool
    {
        return $this->estado === 'publicado';
    }
}