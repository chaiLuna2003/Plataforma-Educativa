<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $name = trim((string) $this->command?->ask(
            'Nombre del administrador',
            'Administrador CUCS'
        ));

        $email = trim((string) $this->command?->ask(
            'Correo del administrador'
        ));

        $password = (string) $this->command?->secret(
            'Contraseña del administrador'
        );

        if (
            $email === ''
            || filter_var($email, FILTER_VALIDATE_EMAIL) === false
        ) {
            throw new RuntimeException(
                'Debes proporcionar un correo electrónico válido.'
            );
        }

        if (strlen($password) < 12) {
            throw new RuntimeException(
                'La contraseña debe contener al menos 12 caracteres.'
            );
        }

        $admin = User::query()->firstOrNew([
            'email' => $email,
        ]);

        $admin->forceFill([
            'name' => $name,
            'password' => Hash::make($password),
            'role' => User::ROLE_ADMIN,
            'plan_id' => null,
            'is_active' => true,
            'email_verified_at' => now(),
            'invited_at' => now(),
            'invited_by' => null,
        ])->save();

        $this->command?->info(
            "Administrador disponible: {$admin->email}"
        );
    }
}
