<?php

namespace Tests\Feature\Student;

use App\Models\Curso;
use App\Models\Leccion;
use App\Models\Modulo;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentLessonProgressTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $student;

    private Plan $plan;

    private Curso $curso;

    private Modulo $modulo;

    private Leccion $leccion;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);

        $this->plan = $this->crearPlan();

        $this->student = $this->crearEstudiante($this->plan);

        $this->curso = $this->crearCurso(
            'Curso principal',
            'publicado'
        );

        $this->plan->cursos()->attach($this->curso);

        $this->modulo = $this->crearModulo(
            $this->curso,
            'Módulo principal',
            'publicado'
        );

        $this->leccion = $this->crearLeccion(
            $this->modulo,
            'Lección principal',
            'publicado'
        );
    }

    public function test_guest_cannot_update_lesson_progress(): void
    {
        $this->put(
            route(
                'student.cursos.lecciones.progreso.update',
                [$this->curso, $this->leccion]
            )
        )->assertRedirect(route('login'));

        $this->assertDatabaseEmpty('leccion_user');
    }

    public function test_admin_cannot_update_student_progress(): void
    {
        $this->actingAs($this->admin)
            ->put(
                route(
                    'student.cursos.lecciones.progreso.update',
                    [$this->curso, $this->leccion]
                )
            )
            ->assertForbidden();

        $this->assertDatabaseEmpty('leccion_user');
    }

    public function test_student_can_complete_published_lesson_from_their_plan(): void
    {
        $this->actingAs($this->student)
            ->put(
                route(
                    'student.cursos.lecciones.progreso.update',
                    [$this->curso, $this->leccion]
                )
            )
            ->assertRedirect(
                route(
                    'student.cursos.lecciones.show',
                    [$this->curso, $this->leccion]
                )
            )
            ->assertSessionHas('status');

        $this->assertDatabaseHas('leccion_user', [
            'user_id' => $this->student->id,
            'leccion_id' => $this->leccion->id,
        ]);

        $this->assertNotNull(
            $this->student
                ->leccionesCompletadas()
                ->firstOrFail()
                ->pivot
                ->completed_at
        );
    }

    public function test_student_can_unmark_completed_lesson(): void
    {
        $this->student
            ->leccionesCompletadas()
            ->attach($this->leccion->id, [
                'completed_at' => now(),
            ]);

        $this->actingAs($this->student)
            ->delete(
                route(
                    'student.cursos.lecciones.progreso.destroy',
                    [$this->curso, $this->leccion]
                )
            )
            ->assertRedirect(
                route(
                    'student.cursos.lecciones.show',
                    [$this->curso, $this->leccion]
                )
            )
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('leccion_user', [
            'user_id' => $this->student->id,
            'leccion_id' => $this->leccion->id,
        ]);
    }

    public function test_completing_lesson_twice_does_not_create_duplicates(): void
    {
        $route = route(
            'student.cursos.lecciones.progreso.update',
            [$this->curso, $this->leccion]
        );

        $this->actingAs($this->student)->put($route);
        $this->actingAs($this->student)->put($route);

        $this->assertDatabaseCount('leccion_user', 1);
    }

    public function test_student_cannot_complete_lesson_outside_their_plan(): void
    {
        $cursoAjeno = $this->crearCurso(
            'Curso ajeno',
            'publicado'
        );

        $moduloAjeno = $this->crearModulo(
            $cursoAjeno,
            'Módulo ajeno',
            'publicado'
        );

        $leccionAjena = $this->crearLeccion(
            $moduloAjeno,
            'Lección ajena',
            'publicado'
        );

        $this->actingAs($this->student)
            ->put(
                route(
                    'student.cursos.lecciones.progreso.update',
                    [$cursoAjeno, $leccionAjena]
                )
            )
            ->assertNotFound();

        $this->assertDatabaseEmpty('leccion_user');
    }

    public function test_student_cannot_complete_draft_lesson(): void
    {
        $leccionBorrador = $this->crearLeccion(
            $this->modulo,
            'Lección borrador',
            'borrador'
        );

        $this->actingAs($this->student)
            ->put(
                route(
                    'student.cursos.lecciones.progreso.update',
                    [$this->curso, $leccionBorrador]
                )
            )
            ->assertNotFound();

        $this->assertDatabaseEmpty('leccion_user');
    }

    public function test_student_cannot_complete_lesson_from_draft_module(): void
    {
        $moduloBorrador = $this->crearModulo(
            $this->curso,
            'Módulo borrador',
            'borrador'
        );

        $leccion = $this->crearLeccion(
            $moduloBorrador,
            'Lección publicada en módulo borrador',
            'publicado'
        );

        $this->actingAs($this->student)
            ->put(
                route(
                    'student.cursos.lecciones.progreso.update',
                    [$this->curso, $leccion]
                )
            )
            ->assertNotFound();

        $this->assertDatabaseEmpty('leccion_user');
    }

    public function test_student_cannot_manipulate_course_in_progress_url(): void
    {
        $otroCurso = $this->crearCurso(
            'Otro curso accesible',
            'publicado'
        );

        $this->plan->cursos()->attach($otroCurso);

        $otroModulo = $this->crearModulo(
            $otroCurso,
            'Otro módulo',
            'publicado'
        );

        $otraLeccion = $this->crearLeccion(
            $otroModulo,
            'Otra lección',
            'publicado'
        );

        $this->actingAs($this->student)
            ->put(
                route(
                    'student.cursos.lecciones.progreso.update',
                    [$this->curso, $otraLeccion]
                )
            )
            ->assertNotFound();

        $this->assertDatabaseEmpty('leccion_user');
    }

    public function test_progress_belongs_only_to_authenticated_student(): void
    {
        $otroEstudiante = $this->crearEstudiante($this->plan);

        $this->actingAs($this->student)
            ->put(
                route(
                    'student.cursos.lecciones.progreso.update',
                    [$this->curso, $this->leccion]
                )
            );

        $this->assertDatabaseHas('leccion_user', [
            'user_id' => $this->student->id,
            'leccion_id' => $this->leccion->id,
        ]);

        $this->assertDatabaseMissing('leccion_user', [
            'user_id' => $otroEstudiante->id,
            'leccion_id' => $this->leccion->id,
        ]);

        $this->actingAs($otroEstudiante)
            ->get(route('student.cursos.show', $this->curso))
            ->assertOk()
            ->assertSee('0%');
    }

    public function test_progress_is_preserved_after_logging_in_again(): void
    {
        $this->actingAs($this->student)
            ->put(
                route(
                    'student.cursos.lecciones.progreso.update',
                    [$this->curso, $this->leccion]
                )
            );

        $this->post(route('logout'))
            ->assertRedirect('/');

        $this->actingAs($this->student)
            ->get(
                route(
                    'student.cursos.lecciones.show',
                    [$this->curso, $this->leccion]
                )
            )
            ->assertOk()
            ->assertSee('Marcar como pendiente');
    }

    public function test_course_progress_percentage_is_calculated_correctly(): void
    {
        $this->crearLeccion(
            $this->modulo,
            'Segunda lección',
            'publicado'
        );

        $moduloBorrador = $this->crearModulo(
            $this->curso,
            'Módulo que no cuenta',
            'borrador'
        );

        $this->crearLeccion(
            $moduloBorrador,
            'Lección que no cuenta',
            'publicado'
        );

        $this->student
            ->leccionesCompletadas()
            ->attach($this->leccion->id, [
                'completed_at' => now(),
            ]);

        $this->actingAs($this->student)
            ->get(route('student.cursos.show', $this->curso))
            ->assertOk()
            ->assertSee('50%')
            ->assertSee('1 de 2 lecciones completadas');
    }

    public function test_course_without_published_lessons_reports_zero_percent(): void
    {
        $cursoVacio = $this->crearCurso(
            'Curso sin lecciones',
            'publicado'
        );

        $this->plan->cursos()->attach($cursoVacio);

        $this->actingAs($this->student)
            ->get(route('student.cursos.show', $cursoVacio))
            ->assertOk()
            ->assertSee('0%')
            ->assertSee('0 de 0 lecciones completadas');
    }

    public function test_student_views_show_progress_and_lesson_status(): void
    {
        $segundaLeccion = $this->crearLeccion(
            $this->modulo,
            'Lección pendiente',
            'publicado'
        );

        $this->student
            ->leccionesCompletadas()
            ->attach($this->leccion->id, [
                'completed_at' => now(),
            ]);

        $this->actingAs($this->student)
            ->get(route('student.cursos.index'))
            ->assertOk()
            ->assertSee('50%')
            ->assertSee('1 de 2 lecciones completadas');

        $this->actingAs($this->student)
            ->get(route('student.cursos.show', $this->curso))
            ->assertOk()
            ->assertSee('Completada')
            ->assertSee('Pendiente')
            ->assertSee($segundaLeccion->titulo);

        $this->actingAs($this->student)
            ->get(
                route(
                    'student.cursos.lecciones.show',
                    [$this->curso, $this->leccion]
                )
            )
            ->assertOk()
            ->assertSee('Marcar como pendiente');
    }

    private function crearPlan(): Plan
    {
        return Plan::create([
            'nombre' => 'Plan activo',
            'slug' => 'plan-activo',
            'descripcion' => 'Plan para pruebas.',
            'estado' => 'activo',
            'orden' => 1,
        ]);
    }

    private function crearEstudiante(?Plan $plan = null): User
    {
        return User::factory()->create([
            'role' => User::ROLE_STUDENT,
            'plan_id' => $plan?->id,
            'is_active' => true,
        ]);
    }

    private function crearCurso(
        string $titulo,
        string $estado
    ): Curso {
        return Curso::create([
            'titulo' => $titulo,
            'slug' => str($titulo)->slug()->toString(),
            'descripcion' => 'Descripción del curso.',
            'nivel' => 'basico',
            'estado' => $estado,
            'orden' => 1,
            'publicado_at' => $estado === 'publicado' ? now() : null,
            'creado_por' => $this->admin->id,
        ]);
    }

    private function crearModulo(
        Curso $curso,
        string $titulo,
        string $estado
    ): Modulo {
        return Modulo::create([
            'curso_id' => $curso->id,
            'titulo' => $titulo,
            'descripcion' => 'Descripción del módulo.',
            'estado' => $estado,
            'orden' => 1,
        ]);
    }

    private function crearLeccion(
        Modulo $modulo,
        string $titulo,
        string $estado
    ): Leccion {
        return Leccion::create([
            'modulo_id' => $modulo->id,
            'titulo' => $titulo,
            'descripcion' => 'Descripción de la lección.',
            'tipo' => 'video',
            'vimeo_video_id' => '123456789',
            'duracion_segundos' => 1800,
            'es_muestra' => false,
            'estado' => $estado,
            'orden' => 1,
            'publicado_at' => $estado === 'publicado' ? now() : null,
        ]);
    }
}
