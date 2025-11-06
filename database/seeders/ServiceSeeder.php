<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema; // Para deshabilitar FKs
use Illuminate\Support\Str; // Para generar slugs únicos
use App\Models\Service;        // El modelo que vamos a llenar
use App\Models\Tramite;        // Necesitamos buscar trámites
use App\Models\Pgob;          // Necesitamos buscar Puntos GOB
use App\Models\service_statuses; // Necesitamos el estado "Activo"

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

    {
        // 1. Preparamos para truncar
        Schema::disableForeignKeyConstraints();
        Service::truncate();
        Schema::enableForeignKeyConstraints();

        // 2. Buscamos los datos necesarios
        $tramites = Tramite::with('institucion')->get()->keyBy('name'); // indexamos por nombre para buscar fácil
        $pgobs = Pgob::all()->keyBy('name'); // indexamos por nombre
        $statusActivo = service_statuses::where('name', 'Activo')->first();

        if (!$statusActivo) {
            $this->command->error('No se encontró el estado "Activo". Por favor, corre ServiceStatusSeeder primero.');
            return;
        }

        // 3. Definimos qué servicios (trámites) se ofrecen dónde y con qué duración
        // ESTRUCTURA: 'Nombre del Pgob' => [ ['Nombre del Tramite', duracion_en_minutos], ... ]
        $servicesDefinition = [
            'Punto GOB Sambil' => [
                ['Emisión de Certificación de No Antecedentes Penales (Papel de Buena Conducta)', 15],
                ['Renovación de Pasaporte (Adulto)', 20],
                ['Solicitud de Pasaporte por Primera Vez (Adulto)', 30],
                ['Renovación de Licencia de Conducir', 15],
                ['Duplicado de Licencia de Conducir', 15],
                ['Solicitud de Acta de Nacimiento', 10],
                ['Solicitud de Acta de Matrimonio', 10],
                ['Afiliación de Titular (Régimen Subsidiado)', 20],
                ['Traspaso de ARS (Régimen Contributivo)', 20],
                ['Reclamación por Alta Facturación Eléctrica', 25],
                ['Presentación de Denuncia o Reclamación', 30],
            ],
            'Punto GOB Megacentro' => [
                ['Emisión de Certificación de No Antecedentes Penales (Papel de Buena Conducta)', 15],
                ['Renovación de Licencia de Conducir', 15],
                ['Duplicado de Licencia de Conducir', 15],
                ['Solicitud de Acta de Nacimiento', 10],
                ['Afiliación de Titular (Régimen Subsidiado)', 20],
                ['Reclamación por Alta Facturación Eléctrica', 25],
                ['Presentación de Denuncia o Reclamación', 30],
            ],
            'Punto GOB Santo Domingo Occidental Mall' => [
                ['Emisión de Certificación de No Antecedentes Penales (Papel de Buena Conducta)', 15],
                ['Renovación de Pasaporte (Adulto)', 20],
                ['Renovación de Licencia de Conducir', 15],
                ['Solicitud de Acta de Nacimiento', 10],
                ['Afiliación de Titular (Régimen Subsidiado)', 20],
            ],
            'Punto GOB Santiago' => [
                ['Emisión de Certificación de No Antecedentes Penales (Papel de Buena Conducta)', 15],
                ['Renovación de Pasaporte (Adulto)', 20],
                ['Renovación de Licencia de Conducir', 15],
                ['Duplicado de Licencia de Conducir', 15],
                ['Solicitud de Acta de Nacimiento', 10],
                ['Reclamación por Alta Facturación Eléctrica', 25], // Asumiendo Edenorte
            ],
            'Punto GOB Colina Centro' => [
                ['Emisión de Certificación de No Antecedentes Penales (Papel de Buena Conducta)', 15],
                ['Renovación de Pasaporte (Adulto)', 20],
                ['Renovación de Licencia de Conducir', 15],
                ['Solicitud de Acta de Nacimiento', 10],
            ],
            'Punto GOB San Cristóbal' => [
                ['Emisión de Certificación de No Antecedentes Penales (Papel de Buena Conducta)', 15],
                ['Renovación de Licencia de Conducir', 15],
                ['Solicitud de Acta de Nacimiento', 10],
                ['Reclamación por Alta Facturación Eléctrica', 25],
            ],
            // Puedes añadir 'Punto GOB Expreso' aquí si ofrece servicios por cita
        ];

        // 4. Iteramos y creamos los registros en la tabla 'services'
        foreach ($servicesDefinition as $pgobName => $tramitesList) {
            if (!isset($pgobs[$pgobName])) {
                $this->command->warn("Omitiendo servicios para '{$pgobName}': Punto GOB no encontrado.");
                continue;
            }
            $currentPgob = $pgobs[$pgobName];

            foreach ($tramitesList as $tramiteInfo) {
                $tramiteName = $tramiteInfo[0];
                $duration = $tramiteInfo[1];

                if (!isset($tramites[$tramiteName])) {
                    $this->command->warn("Omitiendo servicio '{$tramiteName}' en '{$pgobName}': Trámite no encontrado.");
                    continue;
                }
                $currentTramite = $tramites[$tramiteName];


                $baseSlug = Str::slug($currentTramite->name . '-' . $currentPgob->name);
                $slug = $baseSlug;
                $counter = 1;
                // Nos aseguramos de que el slug sea único (aunque ya debería serlo)
                while (Service::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter++;
                }


                Service::create([
                    'tramite_id' => $currentTramite->id,
                    'pgob_id' => $currentPgob->id,
                    'status_id' => $statusActivo->id,
                    'description' => "Servicio de {$currentTramite->name} disponible en {$currentPgob->name}.", // Descripción genérica
                    'duration' => $duration, // Duración específica para este Pgob
                    'slug' => $slug,
                    // 'logo' y 'ubicacion' se pueden dejar null o llenar si tienes datos
                ]);
            }
        }

        $this->command->info('ServiceSeeder ejecutado exitosamente.');
    }


    }


}

