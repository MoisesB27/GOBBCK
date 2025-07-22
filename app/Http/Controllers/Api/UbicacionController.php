<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UbicacionRequest;
use App\Models\Ubicacion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UbicacionController extends Controller
{
    
    public function index(Request $request)
    {
        // Aplicando filtros por ejemplo: nombre, activo, etc
        $ubicaciones = Ubicacion::query()
            ->when($request->filled('search'), fn($q) => $q->where('name', 'like', '%'.$request->search.'%'))
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($ubicaciones);
    }

    /**
     * Guardar nueva ubicación (validada).
     */
    public function store(UbicacionRequest $request)
    {
        $ubicacion = Ubicacion::create($request->validated());
        return response()->json($ubicacion, 201);
    }

    /**
     * Mostrar una ubicación específica.
     */
    public function show(Ubicacion $ubicacion)
    {
        return response()->json($ubicacion);
    }

    /**
     * Actualizar una ubicación (validada).
     */
    public function update(UbicacionRequest $request, Ubicacion $ubicacion)
    {
        $ubicacion->update($request->validated());
        return response()->json($ubicacion);
    }

    /**
     * Eliminar una ubicación.
     */
    public function destroy(Ubicacion $ubicacion)
    {
        $ubicacion->delete();
        return response()->json(null, 204);
    }

}
