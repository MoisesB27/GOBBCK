<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\Instituciones;
use App\Models\Tramite; // Asegúrate de importar tu modelo Tramite

class TramiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // 1. Buscamos las instituciones por su sigla
        $pgr =      Instituciones::where('sigla', 'PGR')->first();
        $dgp =      Instituciones::where('sigla', 'DGP')->first();
        $intrant =  Instituciones::where('sigla', 'INTRANT')->first();
        $jce =      Instituciones::where('sigla', 'JCE')->first();
        $senasa =   Instituciones::where('sigla', 'SeNaSa')->first();
        $sie =      Instituciones::where('sigla', 'SIE')->first();
        $proconsumidor = Instituciones::where('sigla', 'PROCONSUMIDOR')->first();

        // Estructura de campos (ejemplos)
        $cedulaField = [['name' => 'cedula', 'label' => 'Cédula de Identidad', 'type' => 'text']];
        $licenciaFields = [
            ['name' => 'cedula', 'label' => 'Cédula de Identidad', 'type' => 'text'],
            ['name' => 'licencia_no', 'label' => 'No. de Licencia Actual', 'type' => 'text']
        ];
        $pasaporteFields = [
            ['name' => 'cedula', 'label' => 'Cédula de Identidad', 'type' => 'text'],
            ['name' => 'pasaporte_no', 'label' => 'No. de Pasaporte Actual', 'type' => 'text']
        ];

        // 2. Definimos los trámites
        $tramites = [

            // --- PGR ---
            [
                'institucion_id' => $pgr?->id,
                'name' => 'Emisión de Certificación de No Antecedentes Penales (Papel de Buena Conducta)',
                'description' => 'Solicitud de la certificación oficial que indica si un ciudadano posee o no antecedentes penales.',
                'mandatory_fields' => $cedulaField
            ],

            // --- DGP ---
            [
                'institucion_id' => $dgp?->id,
                'name' => 'Renovación de Pasaporte (Adulto)',
                'description' => 'Emisión de una nueva libreta de pasaporte por vencimiento o deterioro.',
                'mandatory_fields' => $pasaporteFields
            ],
            [
                'institucion_id' => $dgp?->id,
                'name' => 'Solicitud de Pasaporte por Primera Vez (Adulto)',
                'description' => 'Emisión de la libreta de pasaporte para ciudadanos mayores de edad que nunca han tenido uno.',
                'mandatory_fields' => $cedulaField
            ],

            // --- INTRANT ---
            [
                'institucion_id' => $intrant?->id,
                'name' => 'Renovación de Licencia de Conducir',
                'description' => 'Proceso para extender la vigencia de la licencia de conducir (todas las categorías).',
                'mandatory_fields' => $licenciaFields
            ],
            [
                'institucion_id' => $intrant?->id,
                'name' => 'Duplicado de Licencia de Conducir',
                'description' => 'Emisión de una nueva licencia en caso de pérdida o robo.',
                'mandatory_fields' => $licenciaFields
            ],
            [
                'institucion_id' => $intrant?->id,
                'name' => 'Examen Práctico de Conducir',
                'description' => 'Prueba de manejo para la obtención de la licencia por primera vez.',
                'mandatory_fields' => $cedulaField
            ],

            // --- JCE ---
            [
                'institucion_id' => $jce?->id,
                'name' => 'Solicitud de Acta de Nacimiento',
                'description' => 'Emisión de una copia certificada del acta de nacimiento.',
                'mandatory_fields' => $cedulaField
            ],
            [
                'institucion_id' => $jce?->id,
                'name' => 'Solicitud de Acta de Matrimonio',
                'description' => 'Emisión de una copia certificada del acta de matrimonio.',
                'mandatory_fields' => $cedulaField
            ],

            // --- SeNaSa ---
            [
                'institucion_id' => $senasa?->id,
                'name' => 'Afiliación de Titular (Régimen Subsidiado)',
                'description' => 'Proceso de inscripción al Seguro Nacional de Salud para el régimen subsidiado.',
                'mandatory_fields' => $cedulaField
            ],
            [
                'institucion_id' => $senasa?->id,
                'name' => 'Traspaso de ARS (Régimen Contributivo)',
                'description' => 'Cambio de Administradora de Riesgos de Salud (ARS) hacia SeNaSa.',
                'mandatory_fields' => $cedulaField
            ],

            // --- SIE ---
            [
                'institucion_id' => $sie?->id,
                'name' => 'Reclamación por Alta Facturación Eléctrica',
                'description' => 'Presentación de una queja formal por montos considerados incorrectos en la factura de luz.',
                'mandatory_fields' => [
                    ['name' => 'cedula', 'label' => 'Cédula de Identidad', 'type' => 'text'],
                    ['name' => 'nic', 'label' => 'Número de Contrato (NIC)', 'type' => 'text']
                ]
            ],

            // --- PROCONSUMIDOR ---
            [
                'institucion_id' => $proconsumidor?->id,
                'name' => 'Presentación de Denuncia o Reclamación',
                'description' => 'Registro formal de una queja contra un proveedor de bienes o servicios.',
                'mandatory_fields' => [
                    ['name' => 'cedula', 'label' => 'Cédula de Identidad', 'type' => 'text'],
                    ['name' => 'rnc_proveedor', 'label' => 'RNC o Nombre del Proveedor', 'type' => 'text']
                ]
            ],

        ];

        // 3. Creamos los trámites en la base de datos
        foreach ($tramites as $tramiteData) {
            // Verificamos que la institución exista antes de intentar crear el trámite
            if (!empty($tramiteData['institucion_id'])) {
                Tramite::create([
                    'institucion_id' => $tramiteData['institucion_id'],
                    'name' => $tramiteData['name'],
                    'description' => $tramiteData['description'],
                    'mandatory_fields' => json_encode($tramiteData['mandatory_fields']), // Codificamos el JSON
                ]);
            } else {
                // Informa en la consola si una institución no se encontró
                $this->command->warn("Omitiendo trámite '{$tramiteData['name']}' porque la institución no fue encontrada.");
            }
        }

        $this->command->info('TramitesSeeder ejecutado exitosamente.');
    }
}
