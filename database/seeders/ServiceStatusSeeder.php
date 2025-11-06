<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\service_statuses; // Asegúrate de importar el modelo

class ServiceStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usamos un array de arrays para updateOrCreate
        $statuses = [
            [
                'name' => 'Activo',
                'color_code' => '#28a745', // Verde
                'description' => 'El servicio está disponible para agendar citas.',
                'is_visible' => true
            ],
            [
                'name' => 'Pausado',
                'color_code' => '#ffc107', // Amarillo
                'description' => 'No permite nuevas citas, pero las citas existentes son válidas. (Ej. Mantenimiento temporal)',
                'is_visible' => true
            ],
            [
                'name' => 'Inactivo',
                'color_code' => '#dc3545', // Rojo
                'description' => 'El servicio no está disponible y no se muestra al público.',
                'is_visible' => false
            ],
            [
                'name' => 'Próximamente',
                'color_code' => '#17a2b8', // Azul info
                'description' => 'El servicio se mostrará pero no permitirá agendar citas.',
                'is_visible' => true
            ],
        ];

        // Usamos updateOrCreate para evitar duplicados si corremos el seeder varias veces
        foreach ($statuses as $status) {
            service_statuses::updateOrCreate(
                ['name' => $status['name']], // Busca por nombre
                $status // Inserta o actualiza el resto de los datos
            );
        }
    }
}
