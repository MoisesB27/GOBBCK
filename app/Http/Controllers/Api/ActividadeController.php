<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Actividade;
use App\Http\Requests\ActividadeRequest as StoreActividadRequest;

class ActividadeController extends Controller

{
    // Listar
    public function index()
    {
        $actividades = Actividade::with('user')->paginate(15);
        return response()->json($actividades);
    }

    // Mostrar
    public function show(string $id)
    {
        $actividad = Actividade::with('user')->findOrFail($id);
        return response()->json($actividad);
    }

    // Crear
    public function store(StoreActividadRequest $request)
    {
        $actividad = Actividade::create($request->validated());
        return response()->json($actividad, 201);
    }

    // Actualizar
    public function update(StoreActividadRequest $request, string $id)
    {
        $actividad = Actividade::findOrFail($id);
        $actividad->update($request->validated());
        return response()->json($actividad);
    }

    // Eliminar
    public function destroy(string $id)
    {
        $actividad = Actividade::findOrFail($id);
        $actividad->delete();
        return response()->json(null, 204);
    }
}