<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TicketStatus;

class TicketStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            // El ID 1 es el valor por defecto que el Request inyecta
            ['id' => 1, 'name' => 'Abierto', 'color_code' => '#1cb817ff', 'description' => 'Ticket recién creado por el usuario. Listo para ser clasificado.'],
            ['id' => 2, 'name' => 'Por asignar', 'color_code' => '#ff8307ff', 'description' => 'Ticket que requiere ser asignado a un administrador de soporte.'],
            ['id' => 3, 'name' => 'Cerrado', 'color_code' => '#077be0ff', 'description' => 'Ticket resuelto y cerrado por un administrador.'],
            ['id' => 4, 'name' => 'Escalado', 'color_code' => '#f01027ff', 'description' => 'Ticket pasado a un nivel superior (ej. al equipo de TI).'],
        ];

        foreach ($statuses as $status) {
            TicketStatus::firstOrCreate(['name' => $status['name']], $status);
        }
    }
}
