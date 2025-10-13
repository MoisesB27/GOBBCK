<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Requests\FormularioRequest as StoreFormularioRequest;
use App\Models\Formulario;
use Illuminate\Http\Request;

class FormularioController extends Controller
{
    public function index(Request $request)
    {
        // 1. Cargamos las nuevas relaciones, incluyendo el nuevo 'status' (FormularioStatus)
        $query = Formulario::with(['user', 'pgob', 'service', 'appointment', 'status']); // <-- Añadido 'status'

        // Filtros opcionales
        if ($request->has('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        // 2. CORRECCIÓN CLAVE: Usamos 'status_id' para filtrar
        // El campo 'status' de texto fue reemplazado por la llave foránea 'status_id'.
        if ($request->has('status_id')) {
            $query->where('status_id', $request->input('status_id'));
        }

        $formularios = $query->paginate(15);

        return response()->json($formularios);
    }

    public function show($id)
    {
        // Cargamos la nueva relación 'status'
        $formulario = Formulario::with(['user', 'pgob', 'service', 'appointment', 'status'])->findOrFail($id);

        return response()->json($formulario);
    }

    public function store(StoreFormularioRequest $request)
    {
        // La validación en StoreFormularioRequest debe asegurar que se envíe 'status_id'
        $formulario = Formulario::create($request->validated());

        return response()->json($formulario, 201);
    }

    public function update(StoreFormularioRequest $request, $id)
    {
        $formulario = Formulario::findOrFail($id);
        $formulario->update($request->validated());

        return response()->json($formulario);
    }

    public function destroy($id)
    {
        $formulario = Formulario::findOrFail($id);
        $formulario->delete();

        return response()->json(null, 204);
    }
}
