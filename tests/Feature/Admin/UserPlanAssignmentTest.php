<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Users\Create;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class UserPlanAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_assign_an_active_plan_when_creating_a_student(): void
    {
        Notification::fake();

        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);

        $plan = Plan::query()->create([
            'nombre' => 'Premium',
            'slug' => 'premium',
            'descripcion' => 'Acceso completo.',
            'estado' => 'activo',
            'orden' => 1,
        ]);

        Livewire::actingAs($admin)
            ->test(Create::class)
            ->set('name', 'Estudiante con plan')
            ->set('email', 'estudiante@example.com')
            ->set('role', User::ROLE_STUDENT)
            ->set('planId', $plan->id)
            ->set('isActive', true)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'estudiante@example.com',
            'role' => User::ROLE_STUDENT,
            'plan_id' => $plan->id,
        ]);
    }

    public function test_student_requires_a_plan_when_account_is_created(): void
    {
        Notification::fake();

        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(Create::class)
            ->set('name', 'Estudiante sin plan')
            ->set('email', 'sinplan@example.com')
            ->set('role', User::ROLE_STUDENT)
            ->set('isActive', true)
            ->call('save')
            ->assertHasErrors([
                'planId' => 'required_if',
            ]);

        $this->assertDatabaseMissing('users', [
            'email' => 'sinplan@example.com',
        ]);
    }

    public function test_inactive_plan_cannot_be_assigned_to_a_student(): void
    {
        Notification::fake();

        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);

        $inactivePlan = Plan::query()->create([
            'nombre' => 'Plan inactivo',
            'slug' => 'plan-inactivo',
            'descripcion' => 'Este plan no debe asignarse.',
            'estado' => 'inactivo',
            'orden' => 1,
        ]);

        Livewire::actingAs($admin)
            ->test(Create::class)
            ->set('name', 'Estudiante inválido')
            ->set('email', 'invalido@example.com')
            ->set('role', User::ROLE_STUDENT)
            ->set('planId', $inactivePlan->id)
            ->set('isActive', true)
            ->call('save')
            ->assertHasErrors([
                'planId' => 'exists',
            ]);

        $this->assertDatabaseMissing('users', [
            'email' => 'invalido@example.com',
        ]);
    }
}
