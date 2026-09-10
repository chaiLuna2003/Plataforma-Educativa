<?php

namespace Tests\Feature\Admin;

use App\Models\Curso;
use App\Models\Modulo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModuleManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_cannot_create_modules(): void
    {
        $admin = $this->crearAdministrador();
        $student = User::factory()->create([
            'role' => User::ROLE_STUDENT,
            'is_active' => true,
        ]);

        $curso = $this->crearCurso($admin);

        $this->actingAs($student)
            ->post(
                route('admin.cursos.modulos.store', $curso),
                [
                    'modulo' => [
                        'titulo' => 'Módulo restringido',
                        'estado' => 'borrador',
                    ],
                ]
            )
            ->assertForbidden();

        $this->assertDatabaseMissing('modulos', [
            'titulo' => 'Módulo restringido',
        ]);
    }

    public function test_admin_can_create_a_module(): void
    {
        $admin = $this->crearAdministrador();
        $curso = $this->crearCurso($admin);

        $response = $this->actingAs($admin)
            ->post(
                route('admin.cursos.modulos.store', $curso),
                [
                    'modulo' => [
                        'titulo' => 'Fundamentos',
                        'descripcion' => 'Contenido introductorio.',
                        'estado' => 'publicado',
                    ],
                ]
            );

        $response
            ->assertRedirect($this->urlModulos($curso))
            ->assertSessionHas(
                'success',
                'Módulo creado correctamente.'
            );

        $this->assertDatabaseHas('modulos', [
            'curso_id' => $curso->id,
            'titulo' => 'Fundamentos',
            'descripcion' => 'Contenido introductorio.',
            'estado' => 'publicado',
            'orden' => 1,
        ]);
    }

    public function test_admin_can_update_a_module(): void
    {
        $admin = $this->crearAdministrador();
        $curso = $this->crearCurso($admin);
        $modulo = $this->crearModulo($curso);

        $response = $this->actingAs($admin)
            ->put(
                route(
                    'admin.cursos.modulos.update',
                    [$curso, $modulo]
                ),
                [
                    'modulo' => [
                        'titulo' => 'Módulo actualizado',
                        'descripcion' => 'Nueva descripción.',
                        'estado' => 'publicado',
                        'orden' => 4,
                    ],
                ]
            );

        $response
            ->assertRedirect($this->urlModulos($curso))
            ->assertSessionHas(
                'success',
                'Módulo actualizado correctamente.'
            );

        $this->assertDatabaseHas('modulos', [
            'id' => $modulo->id,
            'curso_id' => $curso->id,
            'titulo' => 'Módulo actualizado',
            'descripcion' => 'Nueva descripción.',
            'estado' => 'publicado',
            'orden' => 4,
        ]);
    }

    public function test_module_from_another_course_cannot_be_updated(): void
    {
        $admin = $this->crearAdministrador();
        $cursoCorrecto = $this->crearCurso(
            $admin,
            'Curso correcto'
        );

        $otroCurso = $this->crearCurso(
            $admin,
            'Otro curso'
        );

        $modulo = $this->crearModulo($otroCurso);

        $this->actingAs($admin)
            ->put(
                route(
                    'admin.cursos.modulos.update',
                    [$cursoCorrecto, $modulo]
                ),
                [
                    'modulo' => [
                        'titulo' => 'Intento inválido',
                        'estado' => 'publicado',
                        'orden' => 1,
                    ],
                ]
            )
            ->assertNotFound();

        $this->assertDatabaseMissing('modulos', [
            'id' => $modulo->id,
            'titulo' => 'Intento inválido',
        ]);
    }

    public function test_admin_can_delete_a_module_without_lessons(): void
    {
        $admin = $this->crearAdministrador();
        $curso = $this->crearCurso($admin);
        $modulo = $this->crearModulo($curso);

        $this->actingAs($admin)
            ->delete(
                route(
                    'admin.cursos.modulos.destroy',
                    [$curso, $modulo]
                )
            )
            ->assertRedirect($this->urlModulos($curso))
            ->assertSessionHas(
                'success',
                'Módulo eliminado correctamente.'
            );

        $this->assertDatabaseMissing('modulos', [
            'id' => $modulo->id,
        ]);
    }

    public function test_module_with_lessons_cannot_be_deleted(): void
    {
        $admin = $this->crearAdministrador();
        $curso = $this->crearCurso($admin);
        $modulo = $this->crearModulo($curso);

        $modulo->lecciones()->create([
            'titulo' => 'Primera lección',
            'tipo' => 'video',
            'estado' => 'publicado',
            'orden' => 1,
        ]);

        $this->actingAs($admin)
            ->delete(
                route(
                    'admin.cursos.modulos.destroy',
                    [$curso, $modulo]
                )
            )
            ->assertRedirect($this->urlModulos($curso))
            ->assertSessionHas(
                'error',
                'No puedes eliminar un módulo que contiene lecciones.'
            );

        $this->assertDatabaseHas('modulos', [
            'id' => $modulo->id,
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
        string $titulo = 'Curso de prueba'
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

    private function crearModulo(Curso $curso): Modulo
    {
        return Modulo::create([
            'curso_id' => $curso->id,
            'titulo' => 'Módulo inicial',
            'descripcion' => 'Descripción inicial.',
            'estado' => 'borrador',
            'orden' => 1,
        ]);
    }

    private function urlModulos(Curso $curso): string
    {
        return route('admin.cursos.edit', $curso).'#modulos';
    }
}
