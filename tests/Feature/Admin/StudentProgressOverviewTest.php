<?php

namespace Tests\Feature\Admin;

use App\Models\Curso;
use App\Models\Leccion;
use App\Models\Modulo;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentProgressOverviewTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $student;

    private Plan $plan;

    private Curso $course;

    private Modulo $module;

    private Leccion $firstLesson;

    private Leccion $secondLesson;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);

        $this->plan = $this->createPlan();

        $this->student = User::factory()->create([
            'role' => User::ROLE_STUDENT,
            'plan_id' => $this->plan->id,
            'is_active' => true,
        ]);

        $this->course = $this->createCourse(
            'Curso de seguimiento'
        );

        $this->plan->cursos()->attach($this->course);

        $this->module = $this->createModule(
            $this->course,
            'Módulo publicado',
            'publicado'
        );

        $this->firstLesson = $this->createLesson(
            $this->module,
            'Primera lección',
            'publicado'
        );

        $this->secondLesson = $this->createLesson(
            $this->module,
            'Segunda lección',
            'publicado'
        );
    }

    public function test_admin_can_view_student_progress_overview(): void
    {
        $this->student
            ->leccionesCompletadas()
            ->attach($this->firstLesson->id, [
                'completed_at' => now(),
            ]);

        $this->actingAs($this->admin)
            ->get(route('admin.users.show', $this->student))
            ->assertOk()
            ->assertSeeText($this->student->name)
            ->assertSeeText($this->plan->nombre)
            ->assertSeeText($this->course->titulo)
            ->assertSee('50%')
            ->assertSeeText('1 de 2 lecciones')
            ->assertSeeText('Último avance');
    }

    public function test_student_cannot_view_another_student_progress(): void
    {
        $otherStudent = User::factory()->create([
            'role' => User::ROLE_STUDENT,
            'plan_id' => $this->plan->id,
            'is_active' => true,
        ]);

        $this->actingAs($otherStudent)
            ->get(route('admin.users.show', $this->student))
            ->assertForbidden();
    }

    public function test_draft_content_is_excluded_from_progress(): void
    {
        $draftModule = $this->createModule(
            $this->course,
            'Módulo borrador',
            'borrador'
        );

        $this->createLesson(
            $draftModule,
            'Lección publicada dentro de borrador',
            'publicado'
        );

        $this->createLesson(
            $this->module,
            'Lección borrador',
            'borrador'
        );

        $this->student
            ->leccionesCompletadas()
            ->attach($this->firstLesson->id, [
                'completed_at' => now(),
            ]);

        $this->actingAs($this->admin)
            ->get(route('admin.users.show', $this->student))
            ->assertOk()
            ->assertSee('50%')
            ->assertSeeText('1 de 2 lecciones')
            ->assertDontSeeText('Módulo borrador')
            ->assertDontSeeText('Lección borrador');
    }

    public function test_inactive_plan_reports_no_available_courses(): void
    {
        $this->plan->update([
            'estado' => 'inactivo',
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.users.show', $this->student))
            ->assertOk()
            ->assertSeeText('está inactivo')
            ->assertSeeText('No hay cursos disponibles')
            ->assertSee('0%');
    }

    public function test_administrator_cannot_be_viewed_as_student(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.users.show', $this->admin))
            ->assertNotFound();
    }

    private function createPlan(): Plan
    {
        return Plan::create([
            'nombre' => 'Plan académico',
            'slug' => 'plan-academico',
            'descripcion' => 'Plan para seguimiento.',
            'estado' => 'activo',
            'orden' => 1,
        ]);
    }

    private function createCourse(string $title): Curso
    {
        return Curso::create([
            'titulo' => $title,
            'slug' => str($title)->slug()->toString(),
            'descripcion' => 'Descripción del curso.',
            'nivel' => 'basico',
            'estado' => 'publicado',
            'orden' => 1,
            'publicado_at' => now(),
            'creado_por' => $this->admin->id,
        ]);
    }

    private function createModule(
        Curso $course,
        string $title,
        string $status
    ): Modulo {
        return Modulo::create([
            'curso_id' => $course->id,
            'titulo' => $title,
            'descripcion' => 'Descripción del módulo.',
            'estado' => $status,
            'orden' => 1,
        ]);
    }

    private function createLesson(
        Modulo $module,
        string $title,
        string $status
    ): Leccion {
        return Leccion::create([
            'modulo_id' => $module->id,
            'titulo' => $title,
            'descripcion' => 'Descripción de la lección.',
            'tipo' => 'video',
            'vimeo_video_id' => '123456789',
            'duracion_segundos' => 1800,
            'es_muestra' => false,
            'estado' => $status,
            'orden' => 1,
            'publicado_at' => $status === 'publicado'
                ? now()
                : null,
        ]);
    }
}
