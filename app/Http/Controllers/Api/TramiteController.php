<?php

namespace App\Http\Controllers\Api;


use App\Http\Requests\TramiteRequest; // Assuming you have a request class for validation
use App\Http\Controllers\Controller;
use App\Models\Tramite;

class TramiteController extends Controller
{
    public function index()
    {
        $tramites = Tramite::all();
        return response()->json($tramites);
    }

    public function store(TramiteRequest $request)
    {
        $tramite = Tramite::create($request->validated());
        return response()->json($tramite, 201);
    }

    public function storeByInstitucion(TramiteRequest $request, $institucionId)
{
    // Validar datos recibidos según sea necesario
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'institucion_id' => 'sometimes|exists:instituciones,id',
        'mandatory_fields' => 'nullable|array',
    ]);

    // Asociar el trámite con la institución recibida como parámetro
    $validated['institucion_id'] = $institucionId;

    $tramite = Tramite::create($validated);

    return response()->json($tramite, 201);
}

    public function show(Tramite $tramite)
    {
        return response()->json($tramite);
    }

    public function update(TramiteRequest $request, Tramite $tramite)
    {
        $tramite->update($request->validated());
        return response()->json($tramite);
    }

    public function destroy(Tramite $tramite)
    {
        $tramite->delete();
        return response()->json(null, 204);
    }
}
