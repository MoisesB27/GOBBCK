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


        // Llamar al seeder de Puntos GOB
        $this->call(Pgob_Add::class);


        // Llamar al seeder de Instituciones
        $this->call(Institucione_Add::class);

        // Llamar al seeder de Contactos de Instituciones
        $this->call(Instituciones_contac_Add::class);


        // Llamar al seeder de Trámites
        $this->call(TramiteSeeder::class);


        // Llamar al seeder de Servicios_statuses
        $this->call(ServiceStatusSeeder::class);


        // Llamar al seeder de Servicios
        $this->call(ServiceSeeder::class);


        // Llamar al seeder de Appointment Statuses
        $this->call(AppointmentStatusSeeder::class);


        // Llamar al seeder de TicketStatuses
        $this->call(TicketStatusSeeder::class);


        // Llamar al seeder de TicketPriority
        $this->call(TicketPrioritySeeder::class);
    }
}
