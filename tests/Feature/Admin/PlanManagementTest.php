<?php

namespace Tests\Feature\Admin;

use App\Models\Curso;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlanManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admin_can_view_plan_management(): void
    {
        $student = User::factory()->create([
            'role' => User::ROLE_STUDENT,
            'is_active' => true,
        ]);

        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);

        $this->actingAs($student)
            ->get(route('admin.planes.index'))
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('admin.planes.index'))
            ->assertOk()
            ->assertSee('Gestión de planes');
    }

    public function test_admin_can_create_plan_with_independent_courses(): void
    {
        $admin = $this->crearAdministrador();
        $cursoUno = $this->crearCurso($admin, 'Curso clínico');
        $cursoDos = $this->crearCurso($admin, 'Curso avanzado');

        $response = $this->actingAs($admin)
            ->post(route('admin.planes.store'), [
                'nombre' => 'Plan profesional',
                'descripcion' => 'Acceso profesional.',
                'estado' => 'activo',
                'orden' => 4,
                'cursos' => [
                    $cursoUno->id,
                    $cursoDos->id,
                ],
            ]);

        $plan = Plan::query()
            ->where('slug', 'plan-profesional')
            ->firstOrFail();

        $response->assertRedirect(
            route('admin.planes.edit', $plan)
        );

        $this->assertDatabaseHas('planes', [
            'id' => $plan->id,
            'nombre' => 'Plan profesional',
            'estado' => 'activo',
            'orden' => 4,
        ]);

        $this->assertDatabaseHas('curso_plan', [
            'plan_id' => $plan->id,
            'curso_id' => $cursoUno->id,
        ]);

        $this->assertDatabaseHas('curso_plan', [
            'plan_id' => $plan->id,
            'curso_id' => $cursoDos->id,
        ]);
    }

    public function test_admin_can_update_plan_and_sync_its_courses(): void
    {
        $admin = $this->crearAdministrador();
        $cursoAnterior = $this->crearCurso($admin, 'Curso anterior');
        $cursoNuevo = $this->crearCurso($admin, 'Curso nuevo');

        $plan = Plan::create([
            'nombre' => 'Básico',
            'slug' => 'basico',
            'descripcion' => 'Plan inicial.',
            'estado' => 'activo',
            'orden' => 1,
        ]);

        $plan->cursos()->attach($cursoAnterior);

        $response = $this->actingAs($admin)
            ->put(route('admin.planes.update', $plan), [
                'nombre' => 'Básico actualizado',
                'descripcion' => 'Plan actualizado.',
                'estado' => 'activo',
                'orden' => 2,
                'cursos' => [$cursoNuevo->id],
            ]);

        $plan->refresh();

        $response->assertRedirect(
            route('admin.planes.edit', $plan)
        );

        $this->assertSame('basico-actualizado', $plan->slug);

        $this->assertDatabaseMissing('curso_plan', [
            'plan_id' => $plan->id,
            'curso_id' => $cursoAnterior->id,
        ]);

        $this->assertDatabaseHas('curso_plan', [
            'plan_id' => $plan->id,
            'curso_id' => $cursoNuevo->id,
        ]);
    }

    public function test_plan_with_assigned_users_cannot_be_deleted(): void
    {
        $admin = $this->crearAdministrador();

        $plan = Plan::create([
            'nombre' => 'Premium',
            'slug' => 'premium',
            'descripcion' => 'Plan premium.',
            'estado' => 'activo',
            'orden' => 3,
        ]);

        User::factory()->create([
            'plan_id' => $plan->id,
            'role' => User::ROLE_STUDENT,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.planes.destroy', $plan))
            ->assertRedirect(route('admin.planes.index'))
            ->assertSessionHas(
                'error',
                'No puedes eliminar un plan que tiene usuarios asignados.'
            );

        $this->assertDatabaseHas('planes', [
            'id' => $plan->id,
        ]);
    }

    private function crearAdministrador(): User
    {
        return User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);
    }

    private function crearCurso(
        User $admin,
        string $titulo
    ): Curso {
        return Curso::create([
            'titulo' => $titulo,
            'slug' => str($titulo)->slug()->toString(),
            'nivel' => 'basico',
            'estado' => 'publicado',
            'orden' => 0,
            'publicado_at' => now(),
            'creado_por' => $admin->id,
        ]);
    }
}
