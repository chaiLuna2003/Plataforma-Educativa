<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Users\Edit;
use App\Models\Curso;
use App\Models\Leccion;
use App\Models\Modulo;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserPlanUpdateTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $student;

    private Plan $currentPlan;

    private Plan $newPlan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);

        $this->currentPlan = $this->createPlan(
            'Plan actual',
            'plan-actual'
        );

        $this->newPlan = $this->createPlan(
            'Plan nuevo',
            'plan-nuevo'
        );

        $this->student = User::factory()->create([
            'role' => User::ROLE_STUDENT,
            'plan_id' => $this->currentPlan->id,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_change_student_active_plan(): void
    {
        Livewire::actingAs($this->admin)
            ->test(Edit::class, [
                'user' => $this->student,
            ])
            ->assertSet('planId', $this->currentPlan->id)
            ->set('planId', $this->newPlan->id)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(
                route('admin.users.show', $this->student)
            );

        $this->assertDatabaseHas('users', [
            'id' => $this->student->id,
            'plan_id' => $this->newPlan->id,
        ]);
    }

    public function test_inactive_plan_cannot_be_assigned(): void
    {
        $inactivePlan = $this->createPlan(
            'Plan inactivo',
            'plan-inactivo',
            'inactivo'
        );

        Livewire::actingAs($this->admin)
            ->test(Edit::class, [
                'user' => $this->student,
            ])
            ->set('planId', $inactivePlan->id)
            ->call('save')
            ->assertHasErrors([
                'planId' => 'exists',
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->student->id,
            'plan_id' => $this->currentPlan->id,
        ]);
    }

    public function test_student_cannot_access_plan_management(): void
    {
        $otherStudent = User::factory()->create([
            'role' => User::ROLE_STUDENT,
            'plan_id' => $this->currentPlan->id,
            'is_active' => true,
        ]);

        $this->actingAs($otherStudent)
            ->get(route('admin.users.edit', $this->student))
            ->assertForbidden();
    }

    public function test_administrator_account_cannot_be_edited_as_student(): void
    {
        Livewire::actingAs($this->admin)
            ->test(Edit::class, [
                'user' => $this->admin,
            ])
            ->assertNotFound();
    }

    public function test_changing_plan_preserves_historical_progress(): void
    {
        $course = Curso::create([
            'titulo' => 'Curso anterior',
            'slug' => 'curso-anterior',
            'descripcion' => 'Curso del plan anterior.',
            'nivel' => 'basico',
            'estado' => 'publicado',
            'orden' => 1,
            'publicado_at' => now(),
            'creado_por' => $this->admin->id,
        ]);

        $this->currentPlan->cursos()->attach($course);

        $module = Modulo::create([
            'curso_id' => $course->id,
            'titulo' => 'Módulo anterior',
            'descripcion' => 'Contenido anterior.',
            'estado' => 'publicado',
            'orden' => 1,
        ]);

        $lesson = Leccion::create([
            'modulo_id' => $module->id,
            'titulo' => 'Lección completada',
            'descripcion' => 'Lección del plan anterior.',
            'tipo' => 'video',
            'vimeo_video_id' => '123456789',
            'duracion_segundos' => 1800,
            'es_muestra' => false,
            'estado' => 'publicado',
            'orden' => 1,
            'publicado_at' => now(),
        ]);

        $this->student
            ->leccionesCompletadas()
            ->attach($lesson->id, [
                'completed_at' => now(),
            ]);

        Livewire::actingAs($this->admin)
            ->test(Edit::class, [
                'user' => $this->student,
            ])
            ->set('planId', $this->newPlan->id)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('leccion_user', [
            'user_id' => $this->student->id,
            'leccion_id' => $lesson->id,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->student->id,
            'plan_id' => $this->newPlan->id,
        ]);
    }

    private function createPlan(
        string $name,
        string $slug,
        string $status = 'activo'
    ): Plan {
        return Plan::create([
            'nombre' => $name,
            'slug' => $slug,
            'descripcion' => 'Plan para pruebas.',
            'estado' => $status,
            'orden' => 1,
        ]);
    }
}
