<?php

namespace Tests\Feature\Admin;

use App\Models\Curso;
use App\Models\Leccion;
use App\Models\Modulo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_cannot_create_lessons(): void
    {
        $admin = $this->crearAdministrador();
        $student = User::factory()->create([
            'role' => User::ROLE_STUDENT,
            'is_active' => true,
        ]);

        $curso = $this->crearCurso($admin);
        $modulo = $this->crearModulo($curso);

        $this->actingAs($student)
            ->post(
                route(
                    'admin.cursos.modulos.lecciones.store',
                    [$curso, $modulo]
                ),
                [
                    'leccion' => [
                        'titulo' => 'Lección restringida',
                        'vimeo_url' => 'https://vimeo.com/123456789',
                        'estado' => 'publicado',
                    ],
                ]
            )
            ->assertForbidden();

        $this->assertDatabaseMissing('lecciones', [
            'titulo' => 'Lección restringida',
        ]);
    }

    public function test_admin_can_create_a_published_vimeo_lesson(): void
    {
        $admin = $this->crearAdministrador();
        $curso = $this->crearCurso($admin);
        $modulo = $this->crearModulo($curso);

        $response = $this->actingAs($admin)
            ->post(
                route(
                    'admin.cursos.modulos.lecciones.store',
                    [$curso, $modulo]
                ),
                [
                    'leccion' => [
                        'titulo' => 'Introducción clínica',
                        'descripcion' => 'Contenido introductorio.',
                        'vimeo_url' => 'https://vimeo.com/987654321/PrivateHash1',
                        'duracion_minutos' => 90,
                        'es_muestra' => true,
                        'estado' => 'publicado',
                    ],
                ]
            );

        $leccion = Leccion::query()
            ->where('titulo', 'Introducción clínica')
            ->firstOrFail();

        $response
            ->assertRedirect($this->urlModulo($curso, $modulo))
            ->assertSessionHas(
                'success',
                'Lección creada correctamente.'
            );

        $this->assertDatabaseHas('lecciones', [
            'id' => $leccion->id,
            'modulo_id' => $modulo->id,
            'titulo' => 'Introducción clínica',
            'tipo' => 'video',
            'vimeo_video_id' => '987654321',
            'vimeo_video_hash' => 'PrivateHash1',
            'duracion_segundos' => 5400,
            'es_muestra' => true,
            'estado' => 'publicado',
            'orden' => 1,
        ]);

        $this->assertNotNull($leccion->publicado_at);

        $this->assertSame(
            'https://player.vimeo.com/video/987654321?h=PrivateHash1',
            $leccion->vimeo_embed_url
        );
    }

    public function test_admin_can_update_a_lesson(): void
    {
        $admin = $this->crearAdministrador();
        $curso = $this->crearCurso($admin);
        $modulo = $this->crearModulo($curso);
        $leccion = $this->crearLeccion($modulo);

        $response = $this->actingAs($admin)
            ->put(
                route(
                    'admin.cursos.modulos.lecciones.update',
                    [$curso, $modulo, $leccion]
                ),
                [
                    'leccion' => [
                        'titulo' => 'Lección actualizada',
                        'descripcion' => 'Descripción actualizada.',
                        'vimeo_url' => 'https://player.vimeo.com/video/222333444',
                        'duracion_minutos' => 45,
                        'es_muestra' => false,
                        'estado' => 'publicado',
                        'orden' => 3,
                    ],
                ]
            );

        $leccion->refresh();

        $response
            ->assertRedirect($this->urlModulo($curso, $modulo))
            ->assertSessionHas(
                'success',
                'Lección actualizada correctamente.'
            );

        $this->assertSame('Lección actualizada', $leccion->titulo);
        $this->assertSame('222333444', $leccion->vimeo_video_id);
        $this->assertNull($leccion->vimeo_video_hash);
        $this->assertSame(2700, $leccion->duracion_segundos);
        $this->assertFalse($leccion->es_muestra);
        $this->assertSame('publicado', $leccion->estado);
        $this->assertSame(3, $leccion->orden);
        $this->assertNotNull($leccion->publicado_at);

        $this->assertSame(
            'https://player.vimeo.com/video/222333444',
            $leccion->vimeo_embed_url
        );
    }

    public function test_non_vimeo_url_is_rejected(): void
    {
        $admin = $this->crearAdministrador();
        $curso = $this->crearCurso($admin);
        $modulo = $this->crearModulo($curso);

        $this->actingAs($admin)
            ->from(route('admin.cursos.edit', $curso))
            ->post(
                route(
                    'admin.cursos.modulos.lecciones.store',
                    [$curso, $modulo]
                ),
                [
                    'leccion' => [
                        'titulo' => 'Video inválido',
                        'vimeo_url' => 'https://youtube.com/watch/123456789',
                        'es_muestra' => false,
                        'estado' => 'borrador',
                    ],
                ]
            )
            ->assertRedirect(route('admin.cursos.edit', $curso))
            ->assertSessionHasErrors('leccion.vimeo_url');

        $this->assertDatabaseMissing('lecciones', [
            'titulo' => 'Video inválido',
        ]);
    }

    public function test_module_from_another_course_cannot_receive_lessons(): void
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
            ->post(
                route(
                    'admin.cursos.modulos.lecciones.store',
                    [$cursoCorrecto, $modulo]
                ),
                [
                    'leccion' => [
                        'titulo' => 'Lección inválida',
                        'vimeo_url' => 'https://vimeo.com/123456789',
                        'es_muestra' => false,
                        'estado' => 'borrador',
                    ],
                ]
            )
            ->assertNotFound();

        $this->assertDatabaseMissing('lecciones', [
            'titulo' => 'Lección inválida',
        ]);
    }

    public function test_lesson_from_another_module_cannot_be_updated(): void
    {
        $admin = $this->crearAdministrador();
        $curso = $this->crearCurso($admin);

        $moduloCorrecto = $this->crearModulo(
            $curso,
            'Módulo correcto'
        );

        $otroModulo = $this->crearModulo(
            $curso,
            'Otro módulo'
        );

        $leccion = $this->crearLeccion($otroModulo);

        $this->actingAs($admin)
            ->put(
                route(
                    'admin.cursos.modulos.lecciones.update',
                    [$curso, $moduloCorrecto, $leccion]
                ),
                [
                    'leccion' => [
                        'titulo' => 'Intento inválido',
                        'vimeo_url' => 'https://vimeo.com/444555666',
                        'es_muestra' => false,
                        'estado' => 'publicado',
                        'orden' => 2,
                    ],
                ]
            )
            ->assertNotFound();

        $this->assertDatabaseMissing('lecciones', [
            'id' => $leccion->id,
            'titulo' => 'Intento inválido',
        ]);
    }

    public function test_admin_can_delete_a_lesson(): void
    {
        $admin = $this->crearAdministrador();
        $curso = $this->crearCurso($admin);
        $modulo = $this->crearModulo($curso);
        $leccion = $this->crearLeccion($modulo);

        $this->actingAs($admin)
            ->delete(
                route(
                    'admin.cursos.modulos.lecciones.destroy',
                    [$curso, $modulo, $leccion]
                )
            )
            ->assertRedirect($this->urlModulo($curso, $modulo))
            ->assertSessionHas(
                'success',
                'Lección eliminada correctamente.'
            );

        $this->assertDatabaseMissing('lecciones', [
            'id' => $leccion->id,
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

    private function crearModulo(
        Curso $curso,
        string $titulo = 'Módulo inicial'
    ): Modulo {
        return Modulo::create([
            'curso_id' => $curso->id,
            'titulo' => $titulo,
            'descripcion' => 'Descripción inicial.',
            'estado' => 'borrador',
            'orden' => 1,
        ]);
    }

    private function crearLeccion(Modulo $modulo): Leccion
    {
        return Leccion::create([
            'modulo_id' => $modulo->id,
            'titulo' => 'Lección inicial',
            'descripcion' => 'Descripción inicial.',
            'tipo' => 'video',
            'vimeo_video_id' => '111222333',
            'duracion_segundos' => 1800,
            'es_muestra' => false,
            'estado' => 'borrador',
            'orden' => 1,
        ]);
    }

    private function urlModulo(
        Curso $curso,
        Modulo $modulo
    ): string {
        return route('admin.cursos.edit', $curso)
            .'#modulo-'
            .$modulo->id;
    }
}
