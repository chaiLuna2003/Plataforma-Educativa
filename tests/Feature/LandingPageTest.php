<?php

namespace Tests\Feature;

use App\Models\Curso;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);
    }

    public function test_guest_can_visit_public_landing_page(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Plataforma Educativa CUCS')
            ->assertSee('Iniciar sesión')
            ->assertSee('Conoce nuestros cursos')
            ->assertSee('Solicitar prueba gratuita')
            ->assertSee('Testimonios')
            ->assertSee('Preguntas frecuentes')
            ->assertSee('Cambiar apariencia')
            ->assertSee('Contactar por WhatsApp');
    }

    public function test_landing_displays_all_published_courses_and_hides_drafts(): void
    {
        foreach (range(1, 4) as $position) {
            $this->crearCurso(
                "Curso publicado {$position}",
                'publicado',
                $position
            );
        }

        $this->crearCurso(
            'Curso privado en borrador',
            'borrador',
            0
        );

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Curso publicado 1')
            ->assertSee('Curso publicado 2')
            ->assertSee('Curso publicado 3')
            ->assertSee('Curso publicado 4')
            ->assertSee('Carrusel de cursos publicados')
            ->assertDontSee('Curso privado en borrador');
    }

    public function test_whatsapp_buttons_use_configured_number(): void
    {
        config([
            'platform.whatsapp.number' => '525642859995',
        ]);

        $response = $this->get(route('home'));

        $response
            ->assertOk()
            ->assertSee(
                'https://wa.me/525642859995?text='.
                'Hola%2C%20quiero%20solicitar%20informaci%C3%B3n'.
                '%20sobre%20la%20Plataforma%20Educativa%20CUCS.',
                false
            )
            ->assertSee(
                'https://wa.me/525642859995?text='.
                'Hola%2C%20quiero%20solicitar%20una%20prueba'.
                '%20gratuita%20de%20la%20Plataforma%20Educativa'.
                '%20CUCS.',
                false
            );
    }

    private function crearCurso(
        string $titulo,
        string $estado,
        int $orden
    ): Curso {
        return Curso::create([
            'titulo' => $titulo,
            'slug' => str($titulo)->slug()->toString(),
            'descripcion' => "Descripción pública de {$titulo}.",
            'nivel' => 'basico',
            'estado' => $estado,
            'orden' => $orden,
            'publicado_at' => $estado === 'publicado'
                ? now()
                : null,
            'creado_por' => $this->admin->id,
        ]);
    }
}
