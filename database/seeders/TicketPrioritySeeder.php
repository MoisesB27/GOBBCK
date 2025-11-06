<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\TicketPriority;

class TicketPrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $priorities = [
            // Asumiendo que el ID 1 es el valor por defecto ('ordinaria')
            ['id' => 1, 'name' => 'Ordinaria', 'color_code' => '#28a745', 'description' => 'Problema estándar sin impacto inmediato.'],
            ['id' => 2, 'name' => 'Prioritaria', 'color_code' => '#ff5107ff', 'description' => 'Problema que afecta la experiencia de un usuario o servicio.'],
            ['id' => 3, 'name' => 'Urgente', 'color_code' => '#e30b20f7', 'description' => 'Problema que detiene la operación del sistema o causa daño grave.'],
        ];

        foreach ($priorities as $priority) {
            TicketPriority::firstOrCreate(['name' => $priority['name']], $priority);
        }
    }
}
