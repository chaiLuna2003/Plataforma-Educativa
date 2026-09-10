<?php

namespace Tests\Feature\Student;

use App\Models\Curso;
use App\Models\Leccion;
use App\Models\Modulo;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentCourseAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_student_courses(): void
    {
        $this->get(route('student.cursos.index'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_cannot_access_student_courses(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('student.cursos.index'))
            ->assertForbidden();
    }

    public function test_student_only_sees_published_courses_from_active_plan(): void
    {
        $admin = $this->crearAdministrador();
        $plan = $this->crearPlan();
        $student = $this->crearEstudiante($plan);

        $cursoDisponible = $this->crearCurso(
            $admin,
            'Curso disponible',
            'publicado'
        );

        $cursoBorrador = $this->crearCurso(
            $admin,
            'Curso en borrador',
            'borrador'
        );

        $cursoAjeno = $this->crearCurso(
            $admin,
            'Curso de otro plan',
            'publicado'
        );

        $plan->cursos()->attach([
            $cursoDisponible->id,
            $cursoBorrador->id,
        ]);

        $this->actingAs($student)
            ->get(route('student.cursos.index'))
            ->assertOk()
            ->assertSee('Mis cursos')
            ->assertSee('Curso disponible')
            ->assertDontSee('Curso en borrador')
            ->assertDontSee('Curso de otro plan');
    }

    public function test_student_without_plan_sees_empty_course_state(): void
    {
        $student = $this->crearEstudiante();

        $this->actingAs($student)
            ->get(route('student.cursos.index'))
            ->assertOk()
            ->assertSee('Aún no tienes cursos disponibles');
    }

    public function test_student_can_view_published_content_from_assigned_course(): void
    {
        $admin = $this->crearAdministrador();
        $plan = $this->crearPlan();
        $student = $this->crearEstudiante($plan);

        $curso = $this->crearCurso(
            $admin,
            'Curso asignado',
            'publicado'
        );

        $plan->cursos()->attach($curso);

        $moduloPublicado = $this->crearModulo(
            $curso,
            'Módulo publicado',
            'publicado'
        );

        $moduloBorrador = $this->crearModulo(
            $curso,
            'Módulo borrador',
            'borrador'
        );

        $this->crearLeccion(
            $moduloPublicado,
            'Lección publicada',
            'publicado'
        );

        $this->crearLeccion(
            $moduloPublicado,
            'Lección borrador',
            'borrador'
        );

        $this->crearLeccion(
            $moduloBorrador,
            'Lección de módulo oculto',
            'publicado'
        );

        $this->actingAs($student)
            ->get(route('student.cursos.show', $curso))
            ->assertOk()
            ->assertSee('Curso asignado')
            ->assertSee('Módulo publicado')
            ->assertSee('Lección publicada')
            ->assertDontSee('Módulo borrador')
            ->assertDontSee('Lección borrador')
            ->assertDontSee('Lección de módulo oculto');
    }

    public function test_student_cannot_view_course_outside_their_plan(): void
    {
        $admin = $this->crearAdministrador();
        $plan = $this->crearPlan();
        $student = $this->crearEstudiante($plan);

        $curso = $this->crearCurso(
            $admin,
            'Curso restringido',
            'publicado'
        );

        $this->actingAs($student)
            ->get(route('student.cursos.show', $curso))
            ->assertNotFound();
    }

    public function test_student_can_watch_published_vimeo_lesson(): void
    {
        $admin = $this->crearAdministrador();
        $plan = $this->crearPlan();
        $student = $this->crearEstudiante($plan);

        $curso = $this->crearCurso(
            $admin,
            'Curso con video',
            'publicado'
        );

        $plan->cursos()->attach($curso);

        $modulo = $this->crearModulo(
            $curso,
            'Módulo con video',
            'publicado'
        );

        $leccion = $this->crearLeccion(
            $modulo,
            'Video privado',
            'publicado',
            '987654321',
            'PrivateHash1'
        );

        $this->actingAs($student)
            ->get(
                route(
                    'student.cursos.lecciones.show',
                    [$curso, $leccion]
                )
            )
            ->assertOk()
            ->assertSee('Video privado')
            ->assertSee(
                'https://player.vimeo.com/video/987654321?h=PrivateHash1',
                false
            );
    }

    public function test_student_cannot_watch_draft_lesson(): void
    {
        $admin = $this->crearAdministrador();
        $plan = $this->crearPlan();
        $student = $this->crearEstudiante($plan);

        $curso = $this->crearCurso(
            $admin,
            'Curso asignado',
            'publicado'
        );

        $plan->cursos()->attach($curso);

        $modulo = $this->crearModulo(
            $curso,
            'Módulo publicado',
            'publicado'
        );

        $leccion = $this->crearLeccion(
            $modulo,
            'Lección todavía oculta',
            'borrador'
        );

        $this->actingAs($student)
            ->get(
                route(
                    'student.cursos.lecciones.show',
                    [$curso, $leccion]
                )
            )
            ->assertNotFound();
    }

    private function crearAdministrador(): User
    {
        return User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
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

    private function crearCurso(
        User $admin,
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
            'creado_por' => $admin->id,
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
        string $estado,
        string $videoId = '123456789',
        ?string $videoHash = null
    ): Leccion {
        return Leccion::create([
            'modulo_id' => $modulo->id,
            'titulo' => $titulo,
            'descripcion' => 'Descripción de la lección.',
            'tipo' => 'video',
            'vimeo_video_id' => $videoId,
            'vimeo_video_hash' => $videoHash,
            'duracion_segundos' => 1800,
            'es_muestra' => false,
            'estado' => $estado,
            'orden' => 1,
            'publicado_at' => $estado === 'publicado' ? now() : null,
        ]);
    }
}
