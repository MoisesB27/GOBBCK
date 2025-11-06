<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePgobRequest; // El Request que ya tiene la validación fusionada
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Pgob;
use App\Models\Ubicacion; // Modelo para la tabla de ubicaciones
use Illuminate\Support\Facades\Log;

class PgobController extends Controller {
    /**
     * Muestro todos mis Puntos GOB con sus servicios, ubicaciones y administradores.
     */
    public function index()
    {
        // Cargo las relaciones clave, ¡incluyendo la nueva relación 'admins' y 'ubicacions'!
        $pgobs = Pgob::with(['services', 'ubicacions', 'admins'])->paginate(15);

        return response()->json($pgobs);
    }

    /**
     * Muestro un Punto GOB específico con todos sus detalles.
     */
    public function show($id)
    {
        // Cargo el Punto GOB y todas sus relaciones.
        $pgob = Pgob::with(['services', 'ubicacions', 'admins'])->findOrFail($id);

        return response()->json($pgob);
    }

    /**
     * Guardo un nuevo Punto GOB y su Ubicación.
     */
    public function store(StorePgobRequest $request)
    {
        $validatedData = $request->validated();

        try {
            DB::beginTransaction();
            Log::info('PgobController@store: Transacción iniciada.');

            // 1. Separar datos: Los datos de Pgob van a la tabla principal
            $pgobData = [
                'name' => $validatedData['name'],
                'descripcion' => $validatedData['descripcion'],
                'business_hours' => $validatedData['business_hours'],
                'appointment_limit' => $validatedData['appointment_limit'],
                'appointment_limit_per_user' => $validatedData['appointment_limit_per_user'],
                'is_active' => $validatedData['is_active'],
            ];

            // 2. Crear el Punto GOB
            $pgob = Pgob::create($pgobData);
            Log::info('Pgob creado con ID:', ['id' => $pgob->id]);


            // 3. Crear la Ubicación (Usando los datos anidados del request)
            $ubicacionData = array_merge($validatedData['ubicacion'], [
                'pgob_id' => $pgob->id, // Clave foránea esencial
            ]);

            $ubicacion = Ubicacion::create($ubicacionData);
            Log::info('Ubicacion creada con ID:', ['id' => $ubicacion->id, 'pgob_id' => $pgob->id]);

            DB::commit();
            Log::info('Transacción de Pgob y Ubicacion confirmada.');

            // Devolver la respuesta cargando las ubicaciones
            return response()->json($pgob->load('ubicacions'), 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear Pgob y Ubicacion:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Error al guardar el Punto GOB.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Actualizo un Punto GOB existente y su Ubicación. (CORREGIDO)
     */
    public function update(StorePgobRequest $request, $id)
    {
        $pgob = Pgob::with('ubicacions')->findOrFail($id);
        $validatedData = $request->validated();

        try {
            DB::beginTransaction();
            Log::info('PgobController@update: Transacción iniciada.');

            // 1. Separar datos: La Ubicación va en un array aparte
            $ubicacionData = $validatedData['ubicacion'];

            // 2. Extraer los datos de Ubicación del array principal antes de actualizar Pgob.
            // Esto asegura que Eloquent solo vea las columnas de la tabla 'pgobs'.
            $pgobData = array_diff_key($validatedData, array_flip(['ubicacion']));

            // 3. Actualizar el Punto GOB principal
            $pgob->update($pgobData);

            // 4. Actualizar la Ubicación
            // Buscamos la ubicación principal
            $ubicacion = $pgob->ubicacions->first();

            if ($ubicacion) {
                // Actualizar la ubicación existente
                $ubicacion->update($ubicacionData);
                Log::info('Ubicacion ID: ' . $ubicacion->id . ' actualizada.');
            } else {
                    // Si por alguna razón no existe, la creamos (lo cual es un error del store original)
                    $ubicacion = Ubicacion::create(array_merge($ubicacionData, ['pgob_id' => $pgob->id]));
                    Log::warning('Ubicacion no encontrada al actualizar, se creó una nueva con ID: ' . $ubicacion->id);
                }


            DB::commit();
            Log::info('Actualización de Pgob y Ubicacion confirmada.');

            return response()->json($pgob->load('ubicacions'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar Pgob y Ubicacion:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Error al actualizar el Punto GOB.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Elimino un Punto GOB.
     */
    public function destroy($id)
    {
        $pgob = Pgob::findOrFail($id);
        // La eliminación en cascada de la base de datos se encarga de sus ubicaciones y servicios.
        $pgob->delete();

        return response()->json(null, 204);
    }


    /**
     * Encuentra los Puntos GOB más cercanos a la ubicación del usuario,
     * buscando en la tabla 'ubicaciones'. (Método GET)
     */
    public function findnearby(Request $request)
    {

        // 1. Validamos la latitud y longitud del usuario
        $validated = $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        $userLat = $validated['lat'];
        $userLng = $validated['lng'];

        // 2. Preparamos la consulta SQL (Fórmula Haversine en KM)
        $distanceQuery = DB::raw("
            ( 6371 * acos(
                cos( radians(?) )
                * cos( radians( latitude ) )
                * cos( radians( longitude ) - radians(?) )
                + sin( radians(?) )
                * sin( radians( latitude ) )
            ) ) AS distance
        ");

        // 3. Ejecutamos la consulta sobre el modelo 'Ubicacion'
        $locations = Ubicacion::select('*')
            ->with('pgob') // ¡Cargamos el Pgob al que pertenece!
            ->addSelect($distanceQuery)
            ->setBindings([$userLat, $userLng, $userLat]) // Asigna los ? a la query
            ->whereNotNull('latitude') // Ignora ubicaciones sin coordenadas
            ->whereNotNull('longitude')
            ->orderBy('distance', 'asc') // Ordena por el más cercano
            ->limit(5) // Muestra los 5 más cercanos
            ->get();

        // 4. Devolvemos la lista de ubicaciones (que incluyen su Pgob)
        return response()->json($locations);
    }
}
