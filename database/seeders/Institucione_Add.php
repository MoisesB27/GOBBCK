<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Instituciones as inst; // Usando tu alias 'inst'

class Institucione_Add extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $institutions = [
            // Instituciones Principales (Alta Demanda) - La mayoría 'Activa'
            ['nombre' => 'Procuraduría General de la República', 'sigla' => 'PGR', 'Estado' => 'Activa', 'Encargado' => 'Miriam Germán Brito'],
            ['nombre' => 'Dirección General de Pasaportes', 'sigla' => 'DGP', 'Estado' => 'Activa', 'Encargado' => 'Digna Reynoso'],
            ['nombre' => 'Instituto Nacional de Tránsito y Transporte Terrestre', 'sigla' => 'INTRANT', 'Estado' => 'Activa', 'Encargado' => 'Randolfo Rijo Gómez'],
            ['nombre' => 'Junta Central Electoral', 'sigla' => 'JCE', 'Estado' => 'Activa', 'Encargado' => 'Román Jáquez Liranzo'],
            ['nombre' => 'Policía Nacional', 'sigla' => 'PN', 'Estado' => 'Activa', 'Encargado' => 'Ramón Antonio Guzmán Peralta'],

            // Seguridad Social y Subsidios - Mayormente 'Activa'
            ['nombre' => 'Tesorería de la Seguridad Social', 'sigla' => 'TSS', 'Estado' => 'Activa', 'Encargado' => 'Henry Sadhalá'],
            ['nombre' => 'Dirección General de Información y Defensa de los Afiliados a la Seguridad Social', 'sigla' => 'DIDA', 'Estado' => 'Activa', 'Encargado' => 'Carolina Serrata Méndez'],
            ['nombre' => 'Superintendencia de Salud y Riesgos Laborales', 'sigla' => 'SISALRIL', 'Estado' => 'Activa', 'Encargado' => 'Jesús Feris Iglesias'],
            ['nombre' => 'Seguro Nacional de Salud', 'sigla' => 'SeNaSa', 'Estado' => 'Activa', 'Encargado' => 'Santiago Hazim'],
            ['nombre' => 'Administradora de Subsidios Sociales', 'sigla' => 'ADESS', 'Estado' => 'Activa', 'Encargado' => 'Catalino Correa'],
            ['nombre' => 'Programa Supérate', 'sigla' => 'SUPÉRATE', 'Estado' => 'Activa', 'Encargado' => 'Gloria Reyes'],
            ['nombre' => 'Gabinete de Coordinación de Políticas Sociales', 'sigla' => 'GCPS', 'Estado' => 'Activa', 'Encargado' => 'Tony Peña Guaba'],

            // Servicios Públicos y Ministerios - Mayormente 'Activa'
            ['nombre' => 'Superintendencia de Electricidad', 'sigla' => 'SIE', 'Estado' => 'Activa', 'Encargado' => 'Andrés Astacio'],
            ['nombre' => 'Corporación de Acueductos y Alcantarillado de Santo Domingo', 'sigla' => 'CAASD', 'Estado' => 'Activa', 'Encargado' => 'Felipe Suberví'],
            ['nombre' => 'Corporación del Acueducto y Alcantarillado de Santiago', 'sigla' => 'CORAASAN', 'Estado' => 'Activa', 'Encargado' => 'Andrés Burgos'],
            ['nombre' => 'Edenorte Dominicana', 'sigla' => 'EDENORTE', 'Estado' => 'Activa', 'Encargado' => 'Andrés Cueto'],
            ['nombre' => 'Banco de Reservas', 'sigla' => 'BanReservas', 'Estado' => 'Activa', 'Encargado' => 'Samuel Pereyra'],
            ['nombre' => 'Ministerio de Interior y Policía', 'sigla' => 'MIP', 'Estado' => 'Activa', 'Encargado' => 'Jesús Vásquez Martínez'],
            ['nombre' => 'Ministerio de Trabajo', 'sigla' => 'MT', 'Estado' => 'Activa', 'Encargado' => 'Luis Miguel De Camps'],
            ['nombre' => 'Ministerio de Educación Superior, Ciencia y Tecnología', 'sigla' => 'MESCyT', 'Estado' => 'Activa', 'Encargado' => 'Franklin García Fermín'],
            ['nombre' => 'Ministerio de Salud Pública', 'sigla' => 'MSP', 'Estado' => 'Activa', 'Encargado' => 'Víctor Atallah'],

            // Otras Entidades - Estados variados para el dashboard
            ['nombre' => 'Dirección General de Migración', 'sigla' => 'DGM', 'Estado' => 'Activa', 'Encargado' => 'Venancio Alcántara'],
            ['nombre' => 'Dirección General de Jubilaciones y Pensiones a Cargo del Estado', 'sigla' => 'DGJP', 'Estado' => 'Activa', 'Encargado' => 'Juan Rosa'],
            ['nombre' => 'Instituto Nacional de Protección de los Derechos del Consumidor', 'sigla' => 'PROCONSUMIDOR', 'Estado' => 'Activa', 'Encargado' => 'Eddy Alcántara'],

            // Ejemplos de otros estados para el dashboard
            ['nombre' => 'Oficina Nacional de la Propiedad Industrial', 'sigla' => 'ONAPI', 'Estado' => 'Inactiva', 'Encargado' => 'Salvador Ramos'], // Ejemplo de Inactiva
            ['nombre' => 'Consejo Nacional de la Persona Envejeciente', 'sigla' => 'CONAPE', 'Estado' => 'Activa', 'Encargado' => 'José García Ramírez'],
            ['nombre' => 'Ayuntamiento Municipal de Santo Domingo Este', 'sigla' => 'ASDE', 'Estado' => 'Inactiva', 'Encargado' => 'Dio Astacio'], // Ejemplo de Inactiva
            ['nombre' => 'Instituto Nacional de Bienestar Estudiantil', 'sigla' => 'INABIE', 'Estado' => 'Pendiente', 'Encargado' => 'Víctor Castro'], // Ejemplo de Pendiente
            ['nombre' => 'Fideicomiso de Movilidad y Transporte', 'sigla' => 'FIMOVIT', 'Estado' => 'Pendiente', 'Encargado' => 'Gerencia Fideicomiso'], // Ejemplo de Pendiente
        ];

        // Iterar sobre el array y crear o actualizar cada registro
        foreach ($institutions as $inst) {
                inst::updateOrCreate(
                ['sigla' => $inst['sigla']], // Campo único para buscar
                $inst // Datos para insertar o actualizar (ahora incluye 'Encargado')
            );
        }
    }
}

