<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UbicacionRequest; // Usas esto para validar store y update
use App\Models\Ubicacion;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request; // Necesitas importar Request para index

class UbicacionController extends Controller
{
    /**
     * Muestra una lista paginada de ubicaciones.
     * Permite filtrar por Pgob si se envía 'pgob_id' en la query.
     */
    public function index(Request $request)
    {
        $query = Ubicacion::with('pgob'); // Carga la relación con Pgob

        // Filtro opcional por Punto GOB
        if ($request->has('pgob_id')) {
            $query->where('pgob_id', $request->input('pgob_id'));
        }

        $ubicaciones = $query->paginate(15); // Pagina los resultados

        return response()->json($ubicaciones);
    }

    /**
     * Guarda una nueva ubicación en la base de datos.
     * (Este es tu método original, está perfecto)
     */
    public function store(UbicacionRequest $request)
    {
        try {
            $ubicacion = Ubicacion::create($request->validated());
            return response()->json($ubicacion, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al guardar ubicación: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Muestra una ubicación específica por su ID.
     */
    public function show(Ubicacion $ubicacion) // Usamos Route Model Binding
    {
        // Carga la relación con Pgob antes de devolverla
        return response()->json($ubicacion->load('pgob'));
    }

    /**
     * Actualiza una ubicación existente en la base de datos.
     */
    public function update(UbicacionRequest $request, Ubicacion $ubicacion) // Usamos Route Model Binding
    {
        try {
            $ubicacion->update($request->validated());
            return response()->json($ubicacion->load('pgob')); // Devuelve la ubicación actualizada con su Pgob
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar ubicación: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Elimina una ubicación de la base de datos.
     */
    public function destroy(Ubicacion $ubicacion) // Usamos Route Model Binding
    {
        try {
            $ubicacion->delete();
            return response()->json(null, 204); // No Content
        } catch (\Exception $e) {
            // Manejar error (ej. si está protegida por llaves foráneas que no permiten borrar)
            return response()->json(['error' => 'Error al eliminar ubicación: ' . $e->getMessage()], 500);
        }
    }
}
