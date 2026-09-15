<?php

use App\Http\Controllers\Admin\CursoController;
use App\Http\Controllers\Admin\LeccionController;
use App\Http\Controllers\Admin\ModuloController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Student\CursoController as StudentCursoController;
use App\Livewire\Admin\Users\Create as CreateUser;
use App\Livewire\Admin\Users\Edit as EditUser;
use App\Livewire\Admin\Users\Index as UsersIndex;
use App\Livewire\Admin\Users\Show as ShowUser;
use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', [LandingController::class, 'index'])
    ->name('home');

Route::get('dashboard', Dashboard::class)
    ->middleware(['auth', 'active', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'active', 'verified'])
    ->prefix('mis-cursos')
    ->name('student.cursos.')
    ->group(function () {
        Route::get(
            '/',
            [StudentCursoController::class, 'index']
        )->name('index');

        Route::get(
            '{curso}',
            [StudentCursoController::class, 'show']
        )->name('show');

        Route::get(
            '{curso}/lecciones/{leccion}',
            [StudentCursoController::class, 'leccion']
        )->name('lecciones.show');

        Route::put(
            '{curso}/lecciones/{leccion}/progreso',
            [StudentCursoController::class, 'completarLeccion']
        )->name('lecciones.progreso.update');

        Route::delete(
            '{curso}/lecciones/{leccion}/progreso',
            [StudentCursoController::class, 'desmarcarLeccion']
        )->name('lecciones.progreso.destroy');
    });

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

        Route::get('usuarios/{user}', ShowUser::class)
            ->name('users.show');

        Route::get('usuarios/{user}/editar', EditUser::class)
            ->name('users.edit');

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
        Route::resource(
            'planes',
            PlanController::class
        )
            ->parameters(['planes' => 'plan'])
            ->except('show');
    });

require __DIR__.'/auth.php';
