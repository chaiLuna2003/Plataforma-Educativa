<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Inicio')]
class Dashboard extends Component
{
    public function render(): View
    {
        $authenticatedUser = auth()->user();

        $isAdmin = $authenticatedUser instanceof User
            && $authenticatedUser->isAdmin();

        $statistics = [];
        $recentUsers = collect();

        if ($isAdmin) {
            $statistics = [
                'total' => User::query()->count(),

                'students' => User::query()
                    ->where('role', User::ROLE_STUDENT)
                    ->count(),

                'active' => User::query()
                    ->where('is_active', true)
                    ->count(),

                'pendingInvitations' => User::query()
                    ->whereNotNull('invited_at')
                    ->whereNull('email_verified_at')
                    ->count(),
            ];

            $recentUsers = User::query()
                ->latest()
                ->limit(5)
                ->get();
        }

        return view('livewire.dashboard', [
            'isAdmin' => $isAdmin,
            'statistics' => $statistics,
            'recentUsers' => $recentUsers,
        ]);
    }
}
