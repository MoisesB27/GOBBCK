<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UsersSeeder extends Seeder
{
    public function run()
    {
        // Insertar usuario de ejemplo en tabla users
        DB::table('users')->insert([
            'name' => 'Moises',
            'email' => 'moises27@gmail.com',
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('12345678'), // contraseña encriptada
            'remember_token' => Str::random(10),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
