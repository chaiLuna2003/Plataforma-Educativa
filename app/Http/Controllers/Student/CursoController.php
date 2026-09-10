<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use App\Models\Leccion;
use App\Models\Modulo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CursoController extends Controller
{
    public function index(Request $request): View
    {
        $student = $this->obtenerEstudiante($request);

        $plan = $student->plan()
            ->where('estado', 'activo')
            ->first();

        $query = Curso::query()
            ->where('estado', 'publicado')
            ->withCount([
                'modulos' => fn ($query) => $query
                    ->where('estado', 'publicado'),
            ])
            ->orderBy('orden')
            ->orderBy('id');

        if ($plan) {
            $query->whereHas(
                'planes',
                fn ($query) => $query->where(
                    'planes.id',
                    $plan->id
                )
            );
        } else {
            $query->whereRaw('1 = 0');
        }

        $cursos = $query->paginate(12);

        return view('student.cursos.index', [
            'cursos' => $cursos,
            'plan' => $plan,
        ]);
    }

    public function show(
        Request $request,
        Curso $curso
    ): View {
        $student = $this->obtenerEstudiante($request);

        $this->autorizarCurso($student, $curso);

        $curso->load([
            'modulos' => fn ($query) => $query
                ->where('estado', 'publicado')
                ->with([
                    'lecciones' => fn ($query) => $query
                        ->where('estado', 'publicado'),
                ]),
        ]);

        return view('student.cursos.show', [
            'curso' => $curso,
        ]);
    }

    public function leccion(
        Request $request,
        Curso $curso,
        Leccion $leccion
    ): View {
        $student = $this->obtenerEstudiante($request);

        $this->autorizarCurso($student, $curso);

        $leccion->loadMissing('modulo');

        abort_unless(
            $leccion->estado === 'publicado'
            && $leccion->modulo instanceof Modulo
            && $leccion->modulo->curso_id === $curso->id
            && $leccion->modulo->estado === 'publicado',
            404
        );

        $curso->load([
            'modulos' => fn ($query) => $query
                ->where('estado', 'publicado')
                ->with([
                    'lecciones' => fn ($query) => $query
                        ->where('estado', 'publicado'),
                ]),
        ]);

        return view('student.cursos.leccion', [
            'curso' => $curso,
            'leccion' => $leccion,
        ]);
    }

    private function obtenerEstudiante(Request $request): User
    {
        $user = $request->user();

        abort_unless(
            $user instanceof User && $user->isStudent(),
            403
        );

        return $user;
    }

    private function autorizarCurso(
        User $student,
        Curso $curso
    ): void {
        $tieneAcceso = $student->plan()
            ->where('estado', 'activo')
            ->whereHas(
                'cursos',
                fn ($query) => $query->where(
                    'cursos.id',
                    $curso->id
                )
            )
            ->exists();

        abort_unless(
            $curso->estaPublicado() && $tieneAcceso,
            404
        );
    }
}
