<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InstitucionesRequest;
use App\Models\Instituciones; // Tu modelo plural
use App\Models\Tramite;
use App\Models\InstitutionContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InstitucionesController extends Controller
{
    /**
     * Muestra la lista de instituciones con contadores para el dashboard.
     * (Basado en la columna 'Estado')
     */
    public function index(Request $request)
    {
        // 1. Obtenemos los contadores usando la columna 'Estado'
        $counts = [
            'activas' => Instituciones::where('Estado', 'Activa')->count(),
            'inactivas' => Instituciones::where('Estado', 'Inactiva')->count(),
            'pendientes' => Instituciones::where('Estado', 'Pendiente')->count(),
        ];

        // 2. Preparamos la consulta principal para la lista paginada
        $query = Instituciones::with([
            'contacts', // Carga los teléfonos/emails
            'tramites', // Carga los servicios/trámites que ofrece
            'pgobs'     // Carga los Puntos GOB asociados
        ])
        ->withCount([
            'tramites as servicios_count', // Cuenta cuántos servicios tiene
        ]);

        // 3. (Opcional) Añadir filtros
        if ($request->has('search')) {
            $query->where('nombre', 'like', '%' . $request->input('search') . '%')
                    ->orWhere('sigla', 'like', '%' . $request->input('search') . '%');
        }

        if ($request->has('estado')) {
            $query->where('Estado', $request->input('estado'));
        }

        // 4. Ejecutamos la consulta y paginamos
        $instituciones = $query->latest()->paginate(15);

        // 5. Devolvemos una respuesta JSON estructurada para el dashboard
        return response()->json([
            'counts' => $counts,
            'institutions' => $instituciones,
        ]);
    }

    /**
     * Muestra una institución específica con todas sus relaciones.
     */
    public function show($id)
    {
        $institucion = Instituciones::with(['tramites', 'contacts', 'pgobs'])->findOrFail($id);
        return response()->json($institucion);
    }

    /**
     * Guarda una nueva institución, sus contactos, trámites y asociaciones Pgob.
     */
    public function store(InstitucionesRequest $request)
    {
        $validatedData = $request->validated();

        try {
            DB::beginTransaction();

            // 1. Crear la Institución principal
            $institucion = Instituciones::create([
                'nombre' => $validatedData['nombre'],
                'sigla' => $validatedData['sigla'],
                'Encargado' => $validatedData['Encargado'],
                'Estado' => $validatedData['Estado'] ?? 'Pendiente', // Estado por defecto
            ]);

            // 2. Crear los Contactos (Teléfono y Correo)
            // (Usando los nombres de columna 'descripcion' y 'principal' de tu imagen)
            InstitutionContact::create([
                'institucion_id' => $institucion->id,
                'tipo' => 'telefono',
                'valor' => $validatedData['telefono'],
                'descripcion' => 'Teléfono Principal', // Añadido
                'principal' => true, // Usando 'principal'
            ]);

            InstitutionContact::create([
                'institucion_id' => $institucion->id,
                'tipo' => 'correo',
                'valor' => $validatedData['correo_institucional'],
                'descripcion' => 'Correo Institucional', // Añadido
                'principal' => true, // Usando 'principal'
            ]);

            // 3. Asociar Puntos GOB (Relación Muchos-a-Muchos)
            // Espera un array de IDs, ej: [1, 2, 3]
            if (!empty($validatedData['pgob_ids'])) {
                $institucion->pgobs()->sync($validatedData['pgob_ids']);
            }

            // 4. Crear los Trámites/Servicios (Relación Uno-a-Muchos)
            // Espera un array de strings, ej: ["Cambio de cédula", "Acta de nacimiento"]
            if (!empty($validatedData['servicios'])) {
                $tramites = [];
                foreach ($validatedData['servicios'] as $nombreServicio) {
                    $tramites[] = new Tramite(['name' => $nombreServicio]);
                }
                $institucion->tramites()->saveMany($tramites);
            }

            DB::commit();

            return response()->json($institucion->load(['contacts', 'pgobs', 'tramites']), 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al crear institución: " . $e->getMessage());
            return response()->json(['message' => 'Error al crear la institución.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Actualiza una institución existente.
     */
    public function update(InstitucionesRequest $request, $id)
    {
        $institucion = Instituciones::findOrFail($id);
        $validatedData = $request->validated();

        try {
            DB::beginTransaction();

            // 1. Actualizar la Institución principal
            $institucion->update([
                'nombre' => $validatedData['nombre'],
                'sigla' => $validatedData['sigla'],
                'Encargado' => $validatedData['Encargado'],
                'Estado' => $validatedData['Estado'] ?? $institucion->Estado,
            ]);

            // 2. Actualizar Contactos (El método más simple es borrar y recrear)
            $institucion->contacts()->delete(); // Borra los viejos
            InstitutionContact::create([
                'institucion_id' => $institucion->id,
                'tipo' => 'telefono',
                'valor' => $validatedData['telefono'],
                'descripcion' => 'Teléfono Principal', // Añadido
                'principal' => true, // Usando 'principal'
            ]);
            InstitutionContact::create([
                'institucion_id' => $institucion->id,
                'tipo' => 'correo',
                'valor' => $validatedData['correo_institucional'],
                'descripcion' => 'Correo Institucional', // Añadido
                'principal' => true, // Usando 'principal'
            ]);

            // 3. Sincronizar Puntos GOB
            if (isset($validatedData['pgob_ids'])) {
                $institucion->pgobs()->sync($validatedData['pgob_ids']);
            }

            // 4. Sincronizar Trámites/Servicios (Borrar y recrear)
            $institucion->tramites()->delete(); // Borra los viejos
            if (!empty($validatedData['servicios'])) {
                $tramites = [];
                foreach ($validatedData['servicios'] as $nombreServicio) {
                    $tramites[] = new Tramite(['name' => $nombreServicio]);
                }
                $institucion->tramites()->saveMany($tramites);
            }

            DB::commit();

            return response()->json($institucion->load(['contacts', 'pgobs', 'tramites']));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al actualizar institución #{$id}: " . $e->getMessage());
            return response()->json(['message' => 'Error al actualizar la institución.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Elimina una institución.
     */
    public function destroy($id)
    {
        $institucion = Instituciones::findOrFail($id);

        // Las llaves foráneas con onDelete('cascade') deberían borrar
        // automáticamente los trámites, contactos y asociaciones pivote.
        $institucion->delete();

        return response()->json(null, 204);
    }
}

