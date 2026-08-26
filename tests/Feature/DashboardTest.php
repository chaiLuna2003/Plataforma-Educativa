<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $this->get('/dashboard')
            ->assertRedirect('/login');
    }

    public function test_admin_can_view_the_administrative_dashboard(): void
    {
        $admin = User::factory()->create([
            'name' => 'Administrador CUCS',
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);

        $recentUser = User::factory()->create([
            'name' => 'Estudiante Reciente',
            'role' => User::ROLE_STUDENT,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Panel administrativo')
            ->assertSee('Usuarios registrados')
            ->assertSee('Estudiantes')
            ->assertSee('Invitaciones pendientes')
            ->assertSee($recentUser->name);
    }

    public function test_student_can_view_the_student_dashboard(): void
    {
        $student = User::factory()->create([
            'name' => 'Estudiante CUCS',
            'role' => User::ROLE_STUDENT,
            'is_active' => true,
        ]);

        $this->actingAs($student)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Tus cursos aparecerán aquí')
            ->assertSee('Revisar mi perfil')
            ->assertDontSee('Usuarios registrados')
            ->assertDontSee('Usuarios recientes');
    }

    public function test_unverified_user_is_redirected_to_verification_notice(): void
    {
        $user = User::factory()
            ->unverified()
            ->create([
                'is_active' => true,
            ]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertRedirect(route('verification.notice'));
    }
}
