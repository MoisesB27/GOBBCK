<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     *
     * @return void
     */
    public function run(): void
    {
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

        // Aseguramos que solo tenga el rol de superadmin
        $superAdmin->syncRoles(['superadmin']);
        $this->command->info('Usuario SuperAdmin (Moises) asegurado.');
    }
}
