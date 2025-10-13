<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PgobRequest as StorePgobRequest;
use App\Models\Pgob;

class PgobController extends Controller {
    /**
     * Muestro todos mis Puntos GOB con sus servicios, ubicaciones y administradores.
     */
    public function index()
    {
        // Cargo las relaciones clave, ¡incluyendo la nueva relación 'admins'!
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
     * Guardo un nuevo Punto GOB.
     */
    public function store(StorePgobRequest $request)
    {
        $pgob = Pgob::create($request->validated());

        return response()->json($pgob, 201);
    }

    /**
     * Actualizo un Punto GOB existente.
     */
    public function update(StorePgobRequest $request, $id)
    {
        $pgob = Pgob::findOrFail($id);
        $pgob->update($request->validated());

        return response()->json($pgob);
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
}
