<?php

use App\Http\Controllers\Admin\CursoController;
use App\Http\Controllers\Admin\LeccionController;
use App\Http\Controllers\Admin\ModuloController;
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

    Volt::route(
        'settings/profile',
        'settings.profile'
    )->name('settings.profile');

    Volt::route(
        'settings/password',
        'settings.password'
    )->name('settings.password');

    Volt::route(
        'settings/appearance',
        'settings.appearance'
    )->name('settings.appearance');
});

Route::middleware(['auth', 'active', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('usuarios', UsersIndex::class)
            ->name('users.index');

        Route::get('usuarios/crear', CreateUser::class)
            ->name('users.create');

        Route::resource(
            'cursos',
            CursoController::class
        );

        Route::post(
            'cursos/{curso}/modulos',
            [ModuloController::class, 'store']
        )->name('cursos.modulos.store');

        Route::put(
            'cursos/{curso}/modulos/{modulo}',
            [ModuloController::class, 'update']
        )->name('cursos.modulos.update');

        Route::delete(
            'cursos/{curso}/modulos/{modulo}',
            [ModuloController::class, 'destroy']
        )->name('cursos.modulos.destroy');

        Route::post(
            'cursos/{curso}/modulos/{modulo}/lecciones',
            [LeccionController::class, 'store']
        )->name('cursos.modulos.lecciones.store');

        Route::put(
            'cursos/{curso}/modulos/{modulo}/lecciones/{leccion}',
            [LeccionController::class, 'update']
        )->name('cursos.modulos.lecciones.update');

        Route::delete(
            'cursos/{curso}/modulos/{modulo}/lecciones/{leccion}',
            [LeccionController::class, 'destroy']
        )->name('cursos.modulos.lecciones.destroy');
    });

require __DIR__.'/auth.php';