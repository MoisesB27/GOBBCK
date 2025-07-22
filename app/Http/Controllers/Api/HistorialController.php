<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\HistorialRequest as StoreHistorialRequest;
use App\Models\Historial;
use Illuminate\Http\Request;

class HistorialController extends Controller
{
    // Listar historiales
    public function index(Request $request)
    {
        $query = Historial::with(['user', 'tipoServicio', 'entidad', 'appointment', 'appointmentAccessLogs']);

        // Si quieres agregar filtros, aquí los puedes poner, por ejemplo:
        if ($request->has('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        if ($request->has('appointment_id')) {
            $query->where('appointment_id', $request->input('appointment_id'));
        }

        $historiales = $query->paginate(15);

        return response()->json($historiales);
    }


    // Mostrar historial individual
    public function show($id)
    {
        $historial = Historial::with(['user', 'tipoServicio', 'entidad', 'appointment', 'appointmentAccessLogs'])->findOrFail($id);

        return response()->json($historial);
    }


    // Crear nuevo historial
    public function store(StoreHistorialRequest $request)
    {
        $historial = Historial::create($request->validated());

        return response()->json($historial, 201);
    }


    // Actualizar historial
    public function update(StoreHistorialRequest $request, $id)
    {
        $historial = Historial::findOrFail($id);
        $historial->update($request->validated());

        return response()->json($historial);
    }


    // Eliminar historial
    public function destroy($id)
    {
        $historial = Historial::findOrFail($id);
        $historial->delete();

        return response()->json(null, 204);
    }
}
