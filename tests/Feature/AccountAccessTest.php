<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AccountAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware(['web', 'auth', 'active', 'admin'])
            ->get('/testing/admin-only', fn () => response('Autorizado'));
    }

    public function test_active_user_can_access_protected_pages(): void
    {
        $user = User::factory()->create([
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk();
    }

    public function test_inactive_user_is_logged_out_and_redirected(): void
    {
        $user = User::factory()->create([
            'is_active' => false,
        ]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertRedirect(route('login'))
            ->assertSessionHas(
                'account_error',
                'Tu cuenta está desactivada. Contacta al administrador.'
            );

        $this->assertGuest();
    }

    public function test_student_cannot_access_admin_routes(): void
    {
        $student = User::factory()->create([
            'role' => User::ROLE_STUDENT,
            'is_active' => true,
        ]);

        $this->actingAs($student)
            ->get('/testing/admin-only')
            ->assertForbidden();
    }

    public function test_admin_can_access_admin_routes(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get('/testing/admin-only')
            ->assertOk()
            ->assertSee('Autorizado');
    }
}
