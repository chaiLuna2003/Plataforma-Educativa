<?php

use App\Http\Controllers\Admin\CursoController;
use App\Livewire\Admin\Users\Create as CreateUser;
use App\Livewire\Admin\Users\Index as UsersIndex;
use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('dashboard', Dashboard::class)
    ->middleware(['auth', 'active', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'active'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

Route::middleware(['auth', 'active', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('usuarios', UsersIndex::class)
            ->name('users.index');

        Route::get('usuarios/crear', CreateUser::class)
            ->name('users.create');

        Route::resource('cursos', CursoController::class);
    });

require __DIR__.'/auth.php';
