<?php

namespace App\Livewire\Admin\Users;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Editar plan del estudiante')]
class Edit extends Component
{
    public User $user;

    public ?int $planId = null;

    public function mount(User $user): void
    {
        $this->authorizeManagement($user);

        $this->user = $user;
        $this->planId = $user->plan_id;
    }

    public function save(): void
    {
        $this->authorizeManagement($this->user);

        $validated = $this->validate([
            'planId' => [
                'required',
                'integer',
                Rule::exists(Plan::class, 'id')
                    ->where('estado', 'activo'),
            ],
        ]);

        $this->user->update([
            'plan_id' => $validated['planId'],
        ]);

        session()->flash(
            'status',
            'El plan del estudiante fue actualizado correctamente.'
        );

        $this->redirect(
            route('admin.users.show', $this->user),
            navigate: true
        );
    }

    public function render(): View
    {
        $planes = Plan::query()
            ->where('estado', 'activo')
            ->withCount([
                'cursos as cursos_publicados_count' => fn ($query) => $query->where(
                    'estado',
                    'publicado'
                ),
            ])
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get();

        return view('livewire.admin.users.edit', [
            'planes' => $planes,
        ]);
    }

    private function authorizeManagement(User $user): void
    {
        $administrator = auth()->user();

        abort_unless(
            $administrator instanceof User
                && $administrator->isAdmin(),
            403
        );

        abort_unless($user->isStudent(), 404);
    }
}
