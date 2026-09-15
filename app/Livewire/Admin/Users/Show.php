<?php

namespace App\Livewire\Admin\Users;

use App\Models\Curso;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Progreso del estudiante')]
class Show extends Component
{
    public User $user;

    public function mount(User $user): void
    {
        $administrator = auth()->user();

        abort_unless(
            $administrator instanceof User
                && $administrator->isAdmin(),
            403
        );

        abort_unless($user->isStudent(), 404);

        $this->user = $user;
    }

    public function render(): View
    {
        $this->user->loadMissing('plan');

        $plan = $this->user->plan;
        $cursos = collect();

        if ($plan?->estaActivo()) {
            $cursos = Curso::query()
                ->where('estado', 'publicado')
                ->whereHas(
                    'planes',
                    fn ($query) => $query->where(
                        'planes.id',
                        $plan->id
                    )
                )
                ->withCount([
                    'leccionesPublicadas',

                    'leccionesPublicadas as lecciones_completadas_count' => fn ($query) => $query->whereHas(
                        'estudiantesQueCompletaron',
                        fn ($query) => $query->where(
                            'users.id',
                            $this->user->id
                        )
                    ),
                ])
                ->orderBy('orden')
                ->orderBy('id')
                ->get();

            $cursos->each(function (Curso $curso): void {
                $total = (int) $curso->lecciones_publicadas_count;
                $completadas = (int) $curso->lecciones_completadas_count;

                $curso->setAttribute(
                    'porcentaje_progreso',
                    $total > 0
                        ? (int) round(($completadas / $total) * 100)
                        : 0
                );
            });
        }

        $totalLecciones = (int) $cursos->sum(
            'lecciones_publicadas_count'
        );

        $leccionesCompletadas = (int) $cursos->sum(
            'lecciones_completadas_count'
        );

        $porcentajeGeneral = $totalLecciones > 0
            ? (int) round(
                ($leccionesCompletadas / $totalLecciones) * 100
            )
            : 0;

        $ultimaActividad = $this->user
            ->leccionesCompletadas()
            ->max('leccion_user.completed_at');

        return view('livewire.admin.users.show', [
            'plan' => $plan,
            'cursos' => $cursos,
            'totalCursos' => $cursos->count(),
            'totalLecciones' => $totalLecciones,
            'leccionesCompletadas' => $leccionesCompletadas,
            'porcentajeGeneral' => $porcentajeGeneral,
            'ultimaActividad' => $ultimaActividad,
        ]);
    }
}
