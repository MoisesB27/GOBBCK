<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\SoporteRequest as StoreSoporteRequest;
use App\Models\Soporte;
use Illuminate\Support\Facades\Auth;

class SoporteController extends Controller
{
    /**
     * Muestro todos mis tickets de soporte, con filtros y permisos de Punto GOB.
     */
    public function index(Request $request)
    {
        // 1. Inicio la consulta cargando el usuario, Punto GOB y el nuevo estado.
        $query = Soporte::with(['user', 'pgob', 'status']);

        // 2. Lógica de Permisos (¡CRÍTICO!): Filtro si soy Admin GOB.
        $user = Auth::user();
        if ($user && $user->hasRole('admin-gob')) {
            // CORRECCIÓN LÓGICA: uso pluck('id') para obtener los IDs de los modelos Pgob.
            $pgobIds = $user->adminPgobs()->pluck('id');

            // Solo muestro los tickets de los Puntos GOB que administro.
            $query->whereIn('pgob_id', $pgobIds);
        }

        // 3. Filtros del Backoffice (para Super Admin o uso general).
        if ($request->has('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }
        if ($request->has('pgob_id')) {
            $query->where('pgob_id', $request->input('pgob_id'));
        }
        if ($request->has('status_id')) {
            $query->where('status_id', $request->input('status_id'));
        }

        $soportes = $query->latest()->paginate(15);
        return response()->json($soportes);
    }

    /**
     * Muestro un ticket específico.
     */
    public function show($id)
    {
        // Cargo las relaciones (usuario, Punto GOB, y estado).
        $soporte = Soporte::with(['user', 'pgob', 'status'])->findOrFail($id);
        return response()->json($soporte);
    }

    /**
     * Guardo un nuevo ticket de soporte.
     */
    public function store(StoreSoporteRequest $request)
    {
        $soporte = Soporte::create($request->validated());
        return response()->json($soporte, 201);
    }

    /**
     * Actualizo un ticket existente (ej. cambio de estado o asignación).
     */
    public function update(StoreSoporteRequest $request, $id)
    {
        $soporte = Soporte::findOrFail($id);
        $soporte->update($request->validated());
        return response()->json($soporte);
    }

    /**
     * Elimino un ticket de soporte.
     */
    public function destroy($id)
    {
        $soporte = Soporte::findOrFail($id);
        $soporte->delete();
        return response()->json(null, 204);
    }
}
