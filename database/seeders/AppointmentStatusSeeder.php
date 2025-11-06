<?php

namespace Database\Seeders;

use App\Models\AppointmentStatus;
use Illuminate\Database\Seeder;

class AppointmentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Creamos los estados ajustados, forzando los IDs
        $statuses = [
            // Estado inicial si se implementa reserva sin formulario inmediato
            ['id' => 1, 'name' => 'Pendiente',      'color_code' => '#ffc107', 'descripcion' => 'Cita reservada, pendiente de completar formulario o confirmación.'],
            // Estado principal después de confirmar (corresponde a "Activas")
            ['id' => 2, 'name' => 'Activo',         'color_code' => '#28a745', 'descripcion' => 'Cita confirmada y lista para ser atendida.'],
             // Estado después de la atención (corresponde a "Procesadas")
            ['id' => 3, 'name' => 'Procesado',      'color_code' => '#110df0ff', 'descripcion' => 'La cita ya fue atendida y completada.'],
            // Estados finales para cancelaciones (corresponde a "Canceladas")
            ['id' => 4, 'name' => 'Cancelado (Usuario)', 'color_code' => '#ff0202ff', 'descripcion' => 'El ciudadano canceló la cita.'],
            ['id' => 5, 'name' => 'Cancelado (Admin)', 'color_code' => '#ff0202ff', 'descripcion' => 'El personal administrativo canceló la cita.'],
            ['id' => 7, 'name' => 'Cancelado (superadmin)', 'color_code' => '#ff0202ff', 'descripcion' => 'El superadministrador canceló la cita.'],
            // Estado si el usuario no se presenta
            ['id' => 6, 'name' => 'Ausente',        'color_code' => '#6c757d', 'descripcion' => 'El ciudadano no se presentó a la cita.'],
        ];

        foreach ($statuses as $status) {
            // Usamos 'insert' para forzar los IDs después de truncar
            AppointmentStatus::insert($status);
        }
    }
}
