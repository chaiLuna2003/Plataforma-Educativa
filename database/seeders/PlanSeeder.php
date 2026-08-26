<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $planes = [
            [
                'nombre' => 'Básico',
                'slug' => 'basico',
                'descripcion' => 'Acceso a los cursos esenciales seleccionados para este plan.',
                'estado' => 'activo',
                'orden' => 1,
            ],
            [
                'nombre' => 'Intermedio',
                'slug' => 'intermedio',
                'descripcion' => 'Acceso a los cursos seleccionados para el nivel intermedio.',
                'estado' => 'activo',
                'orden' => 2,
            ],
            [
                'nombre' => 'Premium',
                'slug' => 'premium',
                'descripcion' => 'Acceso a los cursos exclusivos seleccionados para este plan.',
                'estado' => 'activo',
                'orden' => 3,
            ],
        ];

        foreach ($planes as $plan) {
            Plan::query()->updateOrCreate(
                ['slug' => $plan['slug']],
                $plan,
            );
        }
    }
}
