<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Instituciones;
use App\Models\InstitutionContact;


class Instituciones_contac_Add extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $contacts = [
            'PGR' => [
                ['tipo' => 'telefono', 'valor' => '809-533-3522', 'desc' => 'Central Telefónica', 'primary' => true],
                ['tipo' => 'correo', 'valor' => 'info@pgr.gob.do', 'desc' => 'Correo General', 'primary' => true],
            ],
            'DGP' => [
                ['tipo' => 'telefono', 'valor' => '809-532-4233', 'desc' => 'Información General', 'primary' => true],
                ['tipo' => 'correo', 'valor' => 'comunicaciones@pasaportes.gob.do', 'desc' => 'Correo General', 'primary' => true],
            ],
            'INTRANT' => [
                ['tipo' => 'telefono', 'valor' => '809-338-6134', 'desc' => 'Central Telefónica', 'primary' => true],
                ['tipo' => 'correo', 'valor' => 'info@intrant.gob.do', 'desc' => 'Correo General', 'primary' => true],
            ],
            'JCE' => [
                ['tipo' => 'telefono', 'valor' => '809-539-5419', 'desc' => 'Oficinas Administrativas', 'primary' => true],
                ['tipo' => 'telefono', 'valor' => '809-537-0188', 'desc' => 'Información Ciudadana', 'primary' => false],
                ['tipo' => 'telefono', 'valor' => '809-200-1959', 'desc' => 'Desde el interior sin cargos', 'primary' => false],
                ['tipo' => 'correo', 'valor' => 'rai@jce.do', 'desc' => 'Acceso a la Información', 'primary' => true],
            ],
            'PN' => [
                ['tipo' => 'telefono', 'valor' => '809-682-2151', 'desc' => 'Central Telefónica', 'primary' => true],
                ['tipo' => 'correo', 'valor' => 'info@policia.gob.do', 'desc' => 'Correo General', 'primary' => true],
            ],
            'TSS' => [
                ['tipo' => 'telefono', 'valor' => '809-472-6363', 'desc' => 'Centro de Asistencia al Usuario', 'primary' => true],
            ],
            'DIDA' => [
                ['tipo' => 'telefono', 'valor' => '809-472-1900', 'desc' => 'Central Telefónica', 'primary' => true],
                ['tipo' => 'correo', 'valor' => 'info@dida.gob.do', 'desc' => 'Correo General', 'primary' => true],
            ],
            'SISALRIL' => [
                ['tipo' => 'telefono', 'valor' => '809-249-1000', 'desc' => 'Central Telefónica', 'primary' => true], // Dato genérico, buscar número público
                ['tipo' => 'correo', 'valor' => 'info@sisalril.gob.do', 'desc' => 'Correo General', 'primary' => true],
            ],
            'SeNaSa' => [
                ['tipo' => 'telefono', 'valor' => '809-701-3821', 'desc' => 'Centro de Llamadas', 'primary' => true],
                ['tipo' => 'telefono', 'valor' => '809-573-6272', 'desc' => 'Interior sin cargos', 'primary' => false],
                ['tipo' => 'whatsapp', 'valor' => '829-472-1710', 'desc' => 'WhatsApp SeNaSa', 'primary' => false],
            ],
            'ADESS' => [
                ['tipo' => 'telefono', 'valor' => '809-565-0009', 'desc' => 'Central Telefónica', 'primary' => true],
                ['tipo' => 'telefono', 'valor' => '809-200-0063', 'desc' => 'Interior sin cargos (opc 1)', 'primary' => false],
                ['tipo' => 'telefono', 'valor' => '809-200-0064', 'desc' => 'Interior sin cargos (opc 2)', 'primary' => false],
                ['tipo' => 'correo', 'valor' => 'info@adess.gob.do', 'desc' => 'Correo General', 'primary' => true],
            ],
            'SUPÉRATE' => [
                ['tipo' => 'telefono', 'valor' => '*462', 'desc' => 'Centro de Contacto (Gratis)', 'primary' => true],
            ],
            'SIE' => [
                ['tipo' => 'telefono', 'valor' => '809-683-2500', 'desc' => 'Central Telefónica', 'primary' => true],
                ['tipo' => 'correo', 'valor' => 'sie@sie.gov.do', 'desc' => 'Correo General', 'primary' => true],
            ],
            'CAASD' => [
                ['tipo' => 'telefono', 'valor' => '809-562-3500', 'desc' => 'Central Telefónica', 'primary' => true],
                ['tipo' => 'correo', 'valor' => 'servicioalcliente@caasd.gob.do', 'desc' => 'Servicio al Cliente', 'primary' => true],
            ],
            'CORAASAN' => [
                ['tipo' => 'telefono', 'valor' => '809-583-4040', 'desc' => 'Central Telefónica', 'primary' => true],
                ['tipo' => 'correo', 'valor' => 'info@coraasan.gob.do', 'desc' => 'Correo General', 'primary' => true],
            ],
            'EDENORTE' => [
                ['tipo' => 'telefono', 'valor' => '809-583-1844', 'desc' => 'Oficina Principal (Santiago)', 'primary' => true],
                ['tipo' => 'telefono', 'valor' => '809-240-1000', 'desc' => 'Call Center', 'primary' => false],
            ],
            'MIP' => [
                ['tipo' => 'telefono', 'valor' => '809-686-6251', 'desc' => 'Central Telefónica', 'primary' => true],
                ['tipo' => 'correo', 'valor' => 'info@mip.gob.do', 'desc' => 'Correo General', 'primary' => true],
            ],
            'MT' => [
                ['tipo' => 'telefono', 'valor' => '809-535-4404', 'desc' => 'Central Telefáctica', 'primary' => true],
                ['tipo' => 'correo', 'valor' => 'info@mt.gob.do', 'desc' => 'Correo General', 'primary' => true],
            ],
            'MESCyT' => [
                ['tipo' => 'telefono', 'valor' => '809-731-1100', 'desc' => 'Central Telefónica', 'primary' => true],
                ['tipo' => 'correo', 'valor' => 'info@mescyt.gob.do', 'desc' => 'Correo General', 'primary' => true],
            ],
            'MSP' => [
                ['tipo' => 'telefono', 'valor' => '809-541-3121', 'desc' => 'Central Telefónica', 'primary' => true],
                ['tipo' => 'correo', 'valor' => 'comunicaciones@salud.gob.do', 'desc' => 'Comunicaciones', 'primary' => true],
            ],
            'DGM' => [
                ['tipo' => 'telefono', 'valor' => '809-508-2555', 'desc' => 'Central Telefónica', 'primary' => true],
                ['tipo' => 'correo', 'valor' => 'info@migracion.gob.do', 'desc' => 'Correo General', 'primary' => true],
            ],
            'PROCONSUMIDOR' => [
                ['tipo' => 'telefono', 'valor' => '809-567-8555', 'desc' => 'Atención al Usuario', 'primary' => true],
                ['tipo' => 'telefono', 'valor' => '809-200-8555', 'desc' => 'Interior sin cargos', 'primary' => false],
                ['tipo' => 'correo', 'valor' => 'info@proconsumidor.gob.do', 'desc' => 'Correo General', 'primary' => true],
            ],
            'ONAPI' => [
                ['tipo' => 'telefono', 'valor' => '809-567-7474', 'desc' => 'Central Telefónica', 'primary' => true],
                ['tipo' => 'whatsapp', 'valor' => '809-567-7474', 'desc' => 'WhatsApp ONAPI', 'primary' => false],
            ],
            'CONAPE' => [
                ['tipo' => 'telefono', 'valor' => '809-688-4433', 'desc' => 'Central Telefónica', 'primary' => true],
                ['tipo' => 'correo', 'valor' => 'info@conape.gob.do', 'desc' => 'Correo General', 'primary' => true],
            ],
            // Faltantes (Datos genéricos)
            'ASDE' => [
                ['tipo' => 'telefono', 'valor' => '809-482-2000', 'desc' => 'Central Telefónica', 'primary' => true],
            ],
            'INABIE' => [
                ['tipo' => 'telefono', 'valor' => '809-532-1320', 'desc' => 'Central Telefónica', 'primary' => true],
            ],
            'FIMOVIT' => [
                ['tipo' => 'telefono', 'valor' => '809-555-0000', 'desc' => 'Dato Pendiente', 'primary' => true], // Dato genérico
            ],
        ];

        foreach ($contacts as $sigla => $contactList) {

            // 1. Buscar la institución por su sigla
            $institution = Instituciones::where('sigla', $sigla)->first();

            if (!$institution) {
                $this->command->info("Omitiendo contactos para {$sigla}: Institución no encontrada.");
                continue;
            }

            // 2. Crear los contactos para esa institución
            foreach ($contactList as $contact) {
                InstitutionContact::create([
                    'institucion_id' => $institution->id,
                    'tipo' => $contact['tipo'],
                    'valor' => $contact['valor'],
                    'descripcion' => $contact['desc'],
                    'principal' => $contact['primary']
                ]);
            }
        }

        $this->command->info('Contactos reales de instituciones cargados exitosamente.');
    }

    }

