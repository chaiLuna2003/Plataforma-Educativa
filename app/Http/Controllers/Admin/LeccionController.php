<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLeccionRequest;
use App\Http\Requests\UpdateLeccionRequest;
use App\Models\Curso;
use App\Models\Leccion;
use App\Models\Modulo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class LeccionController extends Controller
{
    public function store(
        StoreLeccionRequest $request,
        Curso $curso,
        Modulo $modulo
    ): RedirectResponse {
        $this->validarModulo($curso, $modulo);

        $datos = $this->prepararDatos(
            $request->validated()['leccion']
        );

        $datos['orden'] = $datos['orden']
            ?? ((int) $modulo->lecciones()->max('orden') + 1);

        if ($datos['estado'] === 'publicado') {
            $datos['publicado_at'] = now();
        }

        $modulo->lecciones()->create($datos);

        return $this->redirigir($curso, $modulo)
            ->with('success', 'Lección creada correctamente.');
    }

    public function update(
        UpdateLeccionRequest $request,
        Curso $curso,
        Modulo $modulo,
        Leccion $leccion
    ): RedirectResponse {
        $this->validarModulo($curso, $modulo);
        $this->validarLeccion($modulo, $leccion);

        $datos = $this->prepararDatos(
            $request->validated()['leccion']
        );

        $datos['orden'] = $datos['orden'] ?? 0;

        if (
            $datos['estado'] === 'publicado'
            && $leccion->publicado_at === null
        ) {
            $datos['publicado_at'] = now();
        }

        $leccion->update($datos);

        return $this->redirigir($curso, $modulo)
            ->with('success', 'Lección actualizada correctamente.');
    }

    public function destroy(
        Curso $curso,
        Modulo $modulo,
        Leccion $leccion
    ): RedirectResponse {
        $this->validarModulo($curso, $modulo);
        $this->validarLeccion($modulo, $leccion);

        $leccion->delete();

        return $this->redirigir($curso, $modulo)
            ->with('success', 'Lección eliminada correctamente.');
    }

    /**
     * @param  array<string, mixed>  $datos
     * @return array<string, mixed>
     */
    private function prepararDatos(array $datos): array
    {
        $vimeo = $this->extraerDatosVimeo(
            $datos['vimeo_url']
        );

        $duracionMinutos = $datos['duracion_minutos'] ?? null;

        unset(
            $datos['vimeo_url'],
            $datos['duracion_minutos']
        );

        $datos['tipo'] = 'video';
        $datos['vimeo_video_id'] = $vimeo['id'];
        $datos['vimeo_video_hash'] = $vimeo['hash'];

        $datos['duracion_segundos'] = $duracionMinutos
            ? ((int) $duracionMinutos * 60)
            : null;

        return $datos;
    }

    /**
     * @return array{id: string, hash: ?string}
     */
    private function extraerDatosVimeo(string $url): array
    {
        $host = strtolower(
            parse_url($url, PHP_URL_HOST) ?? ''
        );

        $path = trim(
            parse_url($url, PHP_URL_PATH) ?? '',
            '/'
        );

        $segmentos = array_values(
            array_filter(explode('/', $path))
        );

        $videoId = null;
        $hash = null;
        $indiceVideo = null;

        if ($host === 'player.vimeo.com') {
            $indiceVideo = array_search(
                'video',
                $segmentos,
                true
            );

            if (
                $indiceVideo !== false
                && isset($segmentos[$indiceVideo + 1])
                && ctype_digit($segmentos[$indiceVideo + 1])
            ) {
                $videoId = $segmentos[$indiceVideo + 1];
                $indiceVideo++;
            }
        } else {
            foreach ($segmentos as $indice => $segmento) {
                if (ctype_digit($segmento)) {
                    $videoId = $segmento;
                    $indiceVideo = $indice;
                    break;
                }
            }
        }

        $query = parse_url($url, PHP_URL_QUERY) ?? '';
        parse_str($query, $parametros);

        if (
            isset($parametros['h'])
            && is_string($parametros['h'])
        ) {
            $hash = $parametros['h'];
        }

        if (
            $hash === null
            && $indiceVideo !== null
            && isset($segmentos[$indiceVideo + 1])
            && preg_match(
                '/^[a-zA-Z0-9]+$/',
                $segmentos[$indiceVideo + 1]
            )
        ) {
            $hash = $segmentos[$indiceVideo + 1];
        }

        if ($videoId === null) {
            throw ValidationException::withMessages([
                'leccion.vimeo_url' => 'No fue posible obtener el identificador del video de Vimeo.',
            ]);
        }

        return [
            'id' => $videoId,
            'hash' => $hash,
        ];
    }

    private function validarModulo(
        Curso $curso,
        Modulo $modulo
    ): void {
        abort_unless(
            $modulo->curso_id === $curso->id,
            404
        );
    }

    private function validarLeccion(
        Modulo $modulo,
        Leccion $leccion
    ): void {
        abort_unless(
            $leccion->modulo_id === $modulo->id,
            404
        );
    }

    private function redirigir(
        Curso $curso,
        Modulo $modulo
    ): RedirectResponse {
        return redirect()->to(
            route('admin.cursos.edit', $curso)
            .'#modulo-'
            .$modulo->id
        );
    }
}
