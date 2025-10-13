<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Instituciones;
use App\Http\Requests\InstitucionesRequest as StoreInstitucionesRequest;

class InstitucionesController extends Controller
{
    /**
     * Muestro todas mis instituciones con sus trámites, servicios y contactos.
     */
    public function index()
    {
        // Cargo las relaciones clave, incluyendo la nueva relación 'contacts'.
        $instituciones = Instituciones::with(['tramites', 'services', 'contacts'])->paginate(15);
        return response()->json($instituciones);
    }

    /**
     * Muestro una institución específica.
     */
    public function show($id)
    {
        // Cargo la institución y todas sus relaciones (trámites, servicios, contactos).
        $institucion = Instituciones::with(['tramites', 'services', 'contacts'])->findOrFail($id);
        return response()->json($institucion);
    }

    /**
     * Guardo una nueva institución.
     */
    public function store(StoreInstitucionesRequest $request)
    {
        $institucion = Instituciones::create($request->validated());
        return response()->json($institucion, 201);
    }

    /**
     * Actualizo una institución existente.
     */
    public function update(StoreInstitucionesRequest $request, $id)
    {
        $institucion = Instituciones::findOrFail($id);
        $institucion->update($request->validated());
        return response()->json($institucion);
    }

    /**
     * Elimino una institución.
     */
    public function destroy($id)
    {
        $institucion = Instituciones::findOrFail($id);
        // La eliminación en cascada se encargará de borrar trámites, servicios, y contactos asociados.
        $institucion->delete();
        return response()->json(null, 204);
    }
}
