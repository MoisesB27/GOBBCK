<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        // Usuario superadmin existente
        $superAdmin = User::firstOrCreate(
            [
                'email' => 'moises@example.com',
            ],
            [
                'name' => 'Moises Batista',
                'cedula' => '40212345678',
                'password' => Hash::make('Password123@'),
            ]
        );
        $superAdmin->syncRoles(['superadmin']);
        $this->command->info('Usuario SuperAdmin (Moises) asegurado.');

        // Segundo usuario
        $nuevoUsuario = User::firstOrCreate(
            [
                'email' => 'ana@example.com',
            ],
            [
                'name' => 'Ana Gómez',
                'cedula' => '40123456789',
                'password' => Hash::make('PasswordAna123@'),
            ]
        );
        $nuevoUsuario->syncRoles(['usuario']);
        $this->command->info('Usuario Ana Gómez asegurado.');

        // Tercer usuario con rol admin
        $adminUsuario = User::firstOrCreate(
            [
                'email' => 'admin@example.com',
            ],
            [
                'name' => 'Carlos Admin',
                'cedula' => '40345678901',
                'password' => Hash::make('PasswordAdmin123@'),
            ]
        );
        $adminUsuario->syncRoles(['admin']);
        $this->command->info('Usuario Admin (Carlos) asegurado.');
    }
}
