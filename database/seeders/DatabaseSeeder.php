<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Llamar al seeder de roles y permisos
        $this->call(Rolseeder::class);


        // Llamar al seeder de usuarios iniciales
        $this->call(UsersSeeder::class);


    }
}
