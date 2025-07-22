<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests\NotificacionRequest as StoreNotificacionRequest;
use App\Http\Controllers\Controller;
use App\Models\Notificacion;

class NotificacionController extends Controller
{
    // Listar notificaciones con filtros opcionales (usuario, leído, público)
    public function index(Request $request)
    {
        $query = Notificacion::with(['user', 'service', 'pgob']);

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        if ($request->filled('is_read')) {
            $query->where('is_read', $request->boolean('is_read'));
        }

        if ($request->filled('publico')) {
            $query->where('publico', $request->boolean('publico'));
        }

        $notificaciones = $query->orderBy('fecha', 'desc')->paginate(15);

        return response()->json($notificaciones);
    }

    // Mostrar detalle de una notificación
    public function show($id)
    {
        $notificacion = Notificacion::with(['user', 'service', 'pgob'])->findOrFail($id);

        return response()->json($notificacion);
    }

    // Crear una nueva notificación
    public function store(StoreNotificacionRequest $request)
    {
        // Si no se envía 'fecha', asignar ahora
        $data = $request->validated();
        if (empty($data['fecha'])) {
            $data['fecha'] = now();
        }
        $notificacion = Notificacion::create($data);

        return response()->json($notificacion, 201);
    }

    // Actualizar notificación
    public function update(StoreNotificacionRequest $request, $id)
    {
        $notificacion = Notificacion::findOrFail($id);
        $notificacion->update($request->validated());

        return response()->json($notificacion);
    }

    // Eliminar notificación
    public function destroy($id)
    {
        $notificacion = Notificacion::findOrFail($id);
        $notificacion->delete();

        return response()->json(null, 204);
    }
}
