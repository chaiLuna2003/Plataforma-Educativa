<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Leccion extends Model
{
    use HasFactory;

    protected $table = 'lecciones';

    protected $fillable = [
        'modulo_id',
        'titulo',
        'descripcion',
        'tipo',
        'contenido',
        'archivo_path',
        'vimeo_video_id',
        'vimeo_video_hash',
        'miniatura_url',
        'duracion_segundos',
        'es_muestra',
        'orden',
        'estado',
        'publicado_at',
    ];

    protected function casts(): array
    {
        return [
            'duracion_segundos' => 'integer',
            'es_muestra' => 'boolean',
            'orden' => 'integer',
            'publicado_at' => 'datetime',
        ];
    }

    public function modulo(): BelongsTo
    {
        return $this->belongsTo(Modulo::class);
    }

    public function estaPublicada(): bool
    {
        return $this->estado === 'publicado';
    }

    public function esVideo(): bool
    {
        return $this->tipo === 'video';
    }

    public function getVimeoEmbedUrlAttribute(): ?string
    {
        if (! $this->vimeo_video_id) {
            return null;
        }

        $url = 'https://player.vimeo.com/video/'
            .rawurlencode($this->vimeo_video_id);

        if ($this->vimeo_video_hash) {
            $url .= '?h='.rawurlencode($this->vimeo_video_hash);
        }

        return $url;
    }
}
