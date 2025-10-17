<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UbicacionRequest;
use App\Models\Ubicacion;
use App\Http\Controllers\Controller;

class UbicacionController extends Controller
{

    public function store(UbicacionRequest $request)
{
    try {
        $ubicacion = Ubicacion::create($request->validated());

        return response()->json($ubicacion, 201);

    } catch (\Exception $e) {
        return response()->json(['error' => 'Error al guardar ubicación: ' . $e->getMessage()], 500);
    }
}

}
