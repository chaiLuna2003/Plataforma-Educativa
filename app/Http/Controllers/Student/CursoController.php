<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use App\Models\Leccion;
use App\Models\Modulo;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
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

                'leccionesPublicadas',

                'leccionesPublicadas as lecciones_completadas_count' => fn ($query) => $query->whereHas(
                    'estudiantesQueCompletaron',
                    fn ($query) => $query->where(
                        'users.id',
                        $student->id
                    )
                ),
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

        $cursos->getCollection()->each(
            function (Curso $curso): void {
                $total = (int) $curso->lecciones_publicadas_count;
                $completadas = (int) $curso->lecciones_completadas_count;

                $curso->setAttribute(
                    'porcentaje_progreso',
                    $total > 0
                        ? (int) round(($completadas / $total) * 100)
                        : 0
                );
            }
        );

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

        $this->cargarContenidoCurso($curso, $student);

        $progreso = $this->calcularProgresoCurso($curso);

        return view('student.cursos.show', [
            'curso' => $curso,
            'totalLecciones' => $progreso['totalLecciones'],
            'leccionesCompletadas' => $progreso['leccionesCompletadas'],
            'porcentajeProgreso' => $progreso['porcentajeProgreso'],
        ]);
    }

    public function leccion(
        Request $request,
        Curso $curso,
        Leccion $leccion
    ): View {
        $student = $this->obtenerEstudiante($request);

        $this->autorizarLeccion($student, $curso, $leccion);

        $leccion->loadExists([
            'estudiantesQueCompletaron as completada_por_estudiante' => fn ($query) => $query->where(
                'users.id',
                $student->id
            ),
        ]);

        $this->cargarContenidoCurso($curso, $student);

        $progreso = $this->calcularProgresoCurso($curso);

        return view('student.cursos.leccion', [
            'curso' => $curso,
            'leccion' => $leccion,
            'totalLecciones' => $progreso['totalLecciones'],
            'leccionesCompletadas' => $progreso['leccionesCompletadas'],
            'porcentajeProgreso' => $progreso['porcentajeProgreso'],
        ]);
    }

    public function completarLeccion(
        Request $request,
        Curso $curso,
        Leccion $leccion
    ): RedirectResponse {
        $student = $this->obtenerEstudiante($request);

        $this->autorizarLeccion($student, $curso, $leccion);

        $student->leccionesCompletadas()->syncWithoutDetaching([
            $leccion->id => [
                'completed_at' => now(),
            ],
        ]);

        return redirect()
            ->route(
                'student.cursos.lecciones.show',
                [$curso, $leccion]
            )
            ->with(
                'status',
                'La lección se marcó como completada.'
            );
    }

    public function desmarcarLeccion(
        Request $request,
        Curso $curso,
        Leccion $leccion
    ): RedirectResponse {
        $student = $this->obtenerEstudiante($request);

        $this->autorizarLeccion($student, $curso, $leccion);

        $student->leccionesCompletadas()->detach($leccion->id);

        return redirect()
            ->route(
                'student.cursos.lecciones.show',
                [$curso, $leccion]
            )
            ->with(
                'status',
                'La lección volvió a marcarse como pendiente.'
            );
    }

    private function cargarContenidoCurso(
        Curso $curso,
        User $student
    ): void {
        $curso->load([
            'modulos' => fn ($query) => $query
                ->where('estado', 'publicado')
                ->with([
                    'lecciones' => fn ($query) => $query
                        ->where('estado', 'publicado')
                        ->withExists([
                            'estudiantesQueCompletaron as completada_por_estudiante' => fn ($query) => $query->where(
                                'users.id',
                                $student->id
                            ),
                        ]),
                ]),
        ]);
    }

    /**
     * @return array{
     *     totalLecciones: int,
     *     leccionesCompletadas: int,
     *     porcentajeProgreso: int
     * }
     */
    private function calcularProgresoCurso(Curso $curso): array
    {
        $lecciones = $curso->modulos
            ->flatMap(
                fn (Modulo $modulo) => $modulo->lecciones
            );

        $totalLecciones = $lecciones->count();

        $leccionesCompletadas = $lecciones
            ->filter(
                fn (Leccion $leccion) => (bool) $leccion->completada_por_estudiante
            )
            ->count();

        $porcentajeProgreso = $totalLecciones > 0
            ? (int) round(
                ($leccionesCompletadas / $totalLecciones) * 100
            )
            : 0;

        return [
            'totalLecciones' => $totalLecciones,
            'leccionesCompletadas' => $leccionesCompletadas,
            'porcentajeProgreso' => $porcentajeProgreso,
        ];
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

    private function autorizarLeccion(
        User $student,
        Curso $curso,
        Leccion $leccion
    ): void {
        $this->autorizarCurso($student, $curso);

        $leccion->loadMissing('modulo');

        abort_unless(
            $leccion->estaPublicada()
                && $leccion->modulo instanceof Modulo
                && $leccion->modulo->curso_id === $curso->id
                && $leccion->modulo->estaPublicado(),
            404
        );
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
