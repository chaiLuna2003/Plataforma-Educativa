<?php

namespace Tests\Feature\Admin;

use App\Models\Curso;
use App\Models\Modulo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admin_can_view_course_management(): void
    {
        $student = User::factory()->create([
            'role' => User::ROLE_STUDENT,
            'is_active' => true,
        ]);

        $admin = $this->crearAdministrador();

        $this->actingAs($student)
            ->get(route('admin.cursos.index'))
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('admin.cursos.index'))
            ->assertOk()
            ->assertSee('Gestión de cursos');
    }

    public function test_admin_can_create_a_published_course(): void
    {
        $admin = $this->crearAdministrador();

        $response = $this->actingAs($admin)
            ->post(route('admin.cursos.store'), [
                'titulo' => 'Medicina regenerativa básica',
                'descripcion' => 'Introducción al contenido del curso.',
                'nivel' => 'basico',
                'estado' => 'publicado',
                'orden' => 2,
            ]);

        $curso = Curso::query()
            ->where('titulo', 'Medicina regenerativa básica')
            ->firstOrFail();

        $response->assertRedirect(
            route('admin.cursos.edit', $curso)
        );

        $this->assertDatabaseHas('cursos', [
            'id' => $curso->id,
            'titulo' => 'Medicina regenerativa básica',
            'slug' => 'medicina-regenerativa-basica',
            'descripcion' => 'Introducción al contenido del curso.',
            'nivel' => 'basico',
            'estado' => 'publicado',
            'orden' => 2,
            'creado_por' => $admin->id,
        ]);

        $this->assertNotNull($curso->publicado_at);
    }

    public function test_admin_can_update_a_course_and_regenerate_its_slug(): void
    {
        $admin = $this->crearAdministrador();

        $curso = $this->crearCurso(
            $admin,
            'Curso inicial'
        );

        $response = $this->actingAs($admin)
            ->put(route('admin.cursos.update', $curso), [
                'titulo' => 'Curso clínico actualizado',
                'descripcion' => 'Descripción actualizada.',
                'nivel' => 'avanzado',
                'estado' => 'publicado',
                'orden' => 5,
            ]);

        $curso->refresh();

        $response->assertRedirect(
            route('admin.cursos.edit', $curso)
        );

        $this->assertSame(
            'Curso clínico actualizado',
            $curso->titulo
        );

        $this->assertSame(
            'curso-clinico-actualizado',
            $curso->slug
        );

        $this->assertSame('avanzado', $curso->nivel);
        $this->assertSame('publicado', $curso->estado);
        $this->assertSame(5, $curso->orden);
        $this->assertNotNull($curso->publicado_at);
    }

    public function test_admin_can_delete_a_course_without_modules(): void
    {
        $admin = $this->crearAdministrador();

        $curso = $this->crearCurso(
            $admin,
            'Curso eliminable'
        );

        $this->actingAs($admin)
            ->delete(route('admin.cursos.destroy', $curso))
            ->assertRedirect(route('admin.cursos.index'))
            ->assertSessionHas(
                'success',
                'Curso eliminado correctamente.'
            );

        $this->assertDatabaseMissing('cursos', [
            'id' => $curso->id,
        ]);
    }

    public function test_course_with_modules_cannot_be_deleted(): void
    {
        $admin = $this->crearAdministrador();

        $curso = $this->crearCurso(
            $admin,
            'Curso con contenido'
        );

        Modulo::create([
            'curso_id' => $curso->id,
            'titulo' => 'Módulo introductorio',
            'descripcion' => 'Contenido introductorio.',
            'orden' => 1,
            'estado' => 'publicado',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.cursos.destroy', $curso))
            ->assertRedirect(route('admin.cursos.index'))
            ->assertSessionHas(
                'error',
                'No puedes eliminar un curso que contiene módulos. Archívalo primero.'
            );

        $this->assertDatabaseHas('cursos', [
            'id' => $curso->id,
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
            'descripcion' => 'Descripción de prueba.',
            'nivel' => 'basico',
            'estado' => 'borrador',
            'orden' => 0,
            'creado_por' => $admin->id,
        ]);
    }
}
