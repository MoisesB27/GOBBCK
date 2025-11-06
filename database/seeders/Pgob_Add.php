<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pgob;
use App\Models\Ubicacion; // Necesitamos el modelo Ubicacion para crear la Ubicacion asociada
use Illuminate\Support\Facades\Log; // Usaremos Logs para debug
use Illuminate\Support\Facades\DB; // Para transacciones si fuera necesario (aunque updateOrCreate no las necesita aquí)

class Pgob_Add extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Definición de los datos de Pgob y Ubicación (Fusionados en el array)
        $pgobData = [
            [
                "name" => "Punto GOB Megacentro",
                "descripcion" => "Ubicado en Megacentro, entrando por la puerta Botánica, 1er. Nivel. Ave. San Vicente de Paul, Santo Domingo Este.",
                "latitude" => 18.503460, // Datos de Ubicacion
                "longitude" => -69.833110, // Datos de Ubicacion
                "business_hours" => [
                    "monday"    => ["open" => "08:00", "close" => "20:00", "appointments" => true],
                    "tuesday"   => ["open" => "08:00", "close" => "20:00", "appointments" => true],
                    "wednesday" => ["open" => "08:00", "close" => "20:00", "appointments" => true],
                    "thursday"  => ["open" => "08:00", "close" => "20:00", "appointments" => true],
                    "friday"    => ["open" => "08:00", "close" => "20:00", "appointments" => true],
                    "saturday"  => ["open" => "09:00", "close" => "18:00", "appointments" => true],
                    "sunday"    => null
                ],
                "appointment_limit" => 100,
                "appointment_limit_per_user" => 1,
                "is_active" => true
            ],
            // ... (Resto de datos de Puntos GOB) ...
            [
                "name" => "Punto GOB Sambil",
                "descripcion" => "Ubicado en Sambil, Plaza Comercial Sambil, nivel Avenida John F. Kennedy, Distrito Nacional.",
                "latitude" => 18.483980,
                "longitude" => -69.914900,
                "business_hours" => [
                    "monday"    => ["open" => "08:00", "close" => "20:00", "appointments" => true],
                    "tuesday"   => ["open" => "08:00", "close" => "20:00", "appointments" => true],
                    "wednesday" => ["open" => "08:00", "close" => "20:00", "appointments" => true],
                    "thursday"  => ["open" => "08:00", "close" => "20:00", "appointments" => true],
                    "friday"    => ["open" => "08:00", "close" => "20:00", "appointments" => true],
                    "saturday"  => ["open" => "09:00", "close" => "18:00", "appointments" => true],
                    "sunday"    => ["open" => "10:00", "close" => "16:00", "appointments" => false]
                ],
                "appointment_limit" => 150,
                "appointment_limit_per_user" => 1,
                "is_active" => true
            ],
            [
                "name" => "Punto GOB Expreso",
                "descripcion" => "Parada de la Cultura de Santo Domingo Este. C/Marginal Las Américas Este, próximo al Parque Nacional Los Tres Ojos.",
                "latitude" => 18.483320,
                "longitude" => -69.851970,
                "business_hours" => [
                    "monday"    => ["open" => "08:00", "close" => "17:00", "appointments" => true],
                    "tuesday"   => ["open" => "08:00", "close" => "17:00", "appointments" => true],
                    "wednesday" => ["open" => "08:00", "close" => "17:00", "appointments" => true],
                    "thursday"  => ["open" => "08:00", "close" => "17:00", "appointments" => true],
                    "friday"    => ["open" => "08:00", "close" => "17:00", "appointments" => true],
                    "saturday"  => null,
                    "sunday"    => null
                ],
                "appointment_limit" => 80,
                "appointment_limit_per_user" => 1,
                "is_active" => true
            ],
            [
                "name" => "Punto GOB Santo Domingo Occidental Mall",
                "descripcion" => "Ubicado en Occidental mall, Av. Prolongación 27 de febrero, Santo Domingo Oeste.",
                "latitude" => 18.490070,
                "longitude" => -69.972300,
                "business_hours" => [
                    "monday"    => ["open" => "08:00", "close" => "20:00", "appointments" => true],
                    "tuesday"   => ["open" => "08:00", "close" => "20:00", "appointments" => true],
                    "wednesday" => ["open" => "08:00", "close" => "20:00", "appointments" => true],
                    "thursday"  => ["open" => "08:00", "close" => "20:00", "appointments" => true],
                    "friday"    => ["open" => "08:00", "close" => "20:00", "appointments" => true],
                    "saturday"  => ["open" => "09:00", "close" => "18:00", "appointments" => true],
                    "sunday"    => null
                ],
                "appointment_limit" => 100,
                "appointment_limit_per_user" => 1,
                "is_active" => true
            ],
            [
                "name" => "Punto GOB Santiago",
                "descripcion" => "Ubicado en Av. Estrella Sadhalá, Multicentro La Sirena, Santiago de los Caballeros.",
                "latitude" => 19.451890,
                "longitude" => -70.677350,
                "business_hours" => [
                    "monday"    => ["open" => "08:00", "close" => "20:00", "appointments" => true],
                    "tuesday"   => ["open" => "08:00", "close" => "20:00", "appointments" => true],
                    "wednesday" => ["open" => "08:00", "close" => "20:00", "appointments" => true],
                    "thursday"  => ["open" => "08:00", "close" => "20:00", "appointments" => true],
                    "friday"    => ["open" => "08:00", "close" => "20:00", "appointments" => true],
                    "saturday"  => ["open" => "09:00", "close" => "18:00", "appointments" => true],
                    "sunday"    => null
                ],
                "appointment_limit" => 120,
                "appointment_limit_per_user" => 1,
                "is_active" => true
            ],
            [
                "name" => "Punto GOB Colina Centro",
                "descripcion" => "Ubicado en Colina Centro, Plaza Comercial Colina Centro, nivel 1, Avenida Jacobo Majluta Azar, Santo Domingo Norte.",
                "latitude" => 18.544720,
                "longitude" => -69.932910,
                "business_hours" => [
                    "monday"    => ["open" => "08:00", "close" => "20:00", "appointments" => true],
                    "tuesday"   => ["open" => "08:00", "close" => "20:00", "appointments" => true],
                    "wednesday" => ["open" => "08:00", "close" => "20:00", "appointments" => true],
                    "thursday"  => ["open" => "08:00", "close" => "20:00", "appointments" => true],
                    "friday"    => ["open" => "08:00", "close" => "20:00", "appointments" => true],
                    "saturday"  => ["open" => "09:00", "close" => "18:00", "appointments" => true],
                    "sunday"    => null
                ],
                "appointment_limit" => 100,
                "appointment_limit_per_user" => 1,
                "is_active" => true
            ],
            [
                "name" => "Punto GOB San Cristóbal",
                "descripcion" => "Ubicado en Supermercados Bravo, C. El Esfuerzo 3, San Cristóbal.",
                "latitude" => 18.423980,
                "longitude" => -70.106570,
                "business_hours" => [
                    "monday"    => ["open" => "08:00", "close" => "20:00", "appointments" => true],
                    "tuesday"   => ["open" => "08:00", "close" => "20:00", "appointments" => true],
                    "wednesday" => ["open" => "08:00", "close" => "20:00", "appointments" => true],
                    "thursday"  => ["open" => "08:00", "close" => "20:00", "appointments" => true],
                    "friday"    => ["open" => "08:00", "close" => "20:00", "appointments" => true],
                    "saturday"  => ["open" => "09:00", "close" => "18:00", "appointments" => true],
                    "sunday"    => null
                ],
                "appointment_limit" => 90,
                "appointment_limit_per_user" => 1,
                "is_active" => true
            ]
        ];

        // 2. Iterar sobre el array y crear Pgob y Ubicación
        foreach ($pgobData as $punto) {
            // A. Extraer los datos de Ubicación ANTES de crear el Pgob
            $ubicacionData = [
                'latitude' => $punto['latitude'],
                'longitude' => $punto['longitude'],
                'address' => $punto['descripcion'],
                'city' => str_contains($punto['name'], 'Santiago') ? 'Santiago' : (str_contains($punto['name'], 'Colina') ? 'Santo Domingo Norte' : 'Santo Domingo Este'),
                'state' => str_contains($punto['name'], 'Cristóbal') ? 'San Cristóbal' : 'Distrito Nacional',
                'tipo' => 'Principal',
            ];

            // B. Crear el array de datos SÓLO para la tabla 'pgobs'
            $pgobOnlyData = [
                'name' => $punto['name'],
                'descripcion' => $punto['descripcion'],
                'business_hours' => $punto['business_hours'],
                'appointment_limit' => $punto['appointment_limit'],
                'appointment_limit_per_user' => $punto['appointment_limit_per_user'],
                'is_active' => $punto['is_active'],
            ];

            // C. Crear el Pgob (tabla principal)
            $pgob = Pgob::updateOrCreate(
                ['name' => $punto['name']], // Campo único para buscar
                $pgobOnlyData // <-- Usamos el array FILTRADO
            );

            // D. Crear la Ubicacion asociada (tabla relacionada)
            // Se usa el ID del Pgob creado/actualizado
            $ubicacion = Ubicacion::updateOrCreate(
                ['pgob_id' => $pgob->id],
                array_merge($ubicacionData, ['pgob_id' => $pgob->id])
            );

            Log::info("Punto GOB {$pgob->name} creado/actualizado con Ubicacion ID: {$ubicacion->id}");
        }
    }
}
