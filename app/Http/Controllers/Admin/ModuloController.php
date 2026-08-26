<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreModuloRequest;
use App\Http\Requests\UpdateModuloRequest;
use App\Models\Curso;
use App\Models\Modulo;
use Illuminate\Http\RedirectResponse;

class ModuloController extends Controller
{
    public function store(
        StoreModuloRequest $request,
        Curso $curso
    ): RedirectResponse {
        $datos = $request->validated()['modulo'];

        $datos['orden'] = $datos['orden']
            ?? ((int) $curso->modulos()->max('orden') + 1);

        $curso->modulos()->create($datos);

        return $this->redirigirAlCurso($curso)
            ->with('success', 'Módulo creado correctamente.');
    }

    public function update(
        UpdateModuloRequest $request,
        Curso $curso,
        Modulo $modulo
    ): RedirectResponse {
        $this->validarPertenencia($curso, $modulo);

        $datos = $request->validated()['modulo'];
        $datos['orden'] = $datos['orden'] ?? 0;

        $modulo->update($datos);

        return $this->redirigirAlCurso($curso)
            ->with('success', 'Módulo actualizado correctamente.');
    }

    public function destroy(
        Curso $curso,
        Modulo $modulo
    ): RedirectResponse {
        $this->validarPertenencia($curso, $modulo);

        if ($modulo->lecciones()->exists()) {
            return $this->redirigirAlCurso($curso)
                ->with(
                    'error',
                    'No puedes eliminar un módulo que contiene lecciones.'
                );
        }

        $modulo->delete();

        return $this->redirigirAlCurso($curso)
            ->with('success', 'Módulo eliminado correctamente.');
    }

    private function validarPertenencia(
        Curso $curso,
        Modulo $modulo
    ): void {
        abort_unless(
            $modulo->curso_id === $curso->id,
            404
        );
    }

    private function redirigirAlCurso(
        Curso $curso
    ): RedirectResponse {
        return redirect()->to(
            route('admin.cursos.edit', $curso) . '#modulos'
        );
    }
}