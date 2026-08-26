<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Crear usuario')]
class Create extends Component
{
    public string $name = '';

    public string $email = '';

    public string $role = User::ROLE_STUDENT;

    public bool $isActive = true;

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'role' => [
                'required',
                Rule::in([
                    User::ROLE_ADMIN,
                    User::ROLE_STUDENT,
                ]),
            ],
            'isActive' => ['boolean'],
        ]);

        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => Str::lower($validated['email']),
            'password' => Str::random(64),
            'role' => $validated['role'],
            'is_active' => $validated['isActive'],
            'invited_at' => now(),
            'invited_by' => auth()->id(),
        ]);

        $status = Password::sendResetLink([
            'email' => $user->email,
        ]);

        if ($status !== Password::RESET_LINK_SENT) {
            $user->update([
                'is_active' => false,
            ]);

            $this->addError(
                'email',
                'La cuenta fue creada, pero no fue posible enviar la invitación. La cuenta quedó inactiva.'
            );

            return;
        }

        session()->flash(
            'status',
            'La cuenta fue creada y la invitación fue enviada correctamente.'
        );

        $this->redirect(
            route('admin.users.index'),
            navigate: true
        );
    }

    public function render(): View
    {
        return view('livewire.admin.users.create');
    }
}
