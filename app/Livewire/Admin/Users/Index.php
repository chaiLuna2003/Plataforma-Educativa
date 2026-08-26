<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Administración de usuarios')]
class Index extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: 'all')]
    public string $role = 'all';

    #[Url(except: 'all')]
    public string $status = 'all';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedRole(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'role', 'status']);
        $this->resetPage();
    }

    public function toggleStatus(int $userId): void
    {
        $user = User::query()->findOrFail($userId);

        if ($user->getKey() === auth()->id()) {
            $this->addError(
                'account',
                'No puedes desactivar tu propia cuenta.'
            );

            return;
        }

        $user->update([
            'is_active' => ! $user->isActive(),
        ]);

        session()->flash(
            'status',
            $user->isActive()
                ? 'La cuenta fue activada correctamente.'
                : 'La cuenta fue desactivada correctamente.'
        );
    }

    public function render(): View
    {
        $users = User::query()
            ->when(
                $this->search !== '',
                fn ($query) => $query->where(function ($query) {
                    $query
                        ->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                })
            )
            ->when(
                $this->role !== 'all',
                fn ($query) => $query->where('role', $this->role)
            )
            ->when(
                $this->status === 'active',
                fn ($query) => $query->where('is_active', true)
            )
            ->when(
                $this->status === 'inactive',
                fn ($query) => $query->where('is_active', false)
            )
            ->latest()
            ->paginate(10);

        return view('livewire.admin.users.index', [
            'users' => $users,
        ]);
    }
}
