<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Requests\Storeappointment_access_logRequest as Storeappointment_access_logRequest;
use App\Models\appointment_access_log;
use Illuminate\Http\Request;


class AppointmentAccessLogController extends Controller
{
     // Listar logs (pueden usarse filtros opcionales)
    public function index(Request $request)
    {
        $query = appointment_access_log::query();

        // Filtro opcional por appointment_id
        if ($request->has('appointment_id')) {
            $query->where('appointment_id', $request->input('appointment_id'));
        }

        // Paginación
        $logs = $query->with('appointment')->paginate(15);

        return response()->json($logs);
    }

    // Mostrar un log específico
    public function show($id)
    {
        $log = appointment_access_log::with('appointment')->findOrFail($id);

        return response()->json($log);
    }

    // Crear un log nuevo
    public function store(Storeappointment_access_logRequest $request)
    {
        $data = $request->validated();

        // Si accessed_at no fue enviado, se asigna fecha actual automáticamente en modelo/migración
        $log = appointment_access_log::create($data);

        return response()->json($log, 201);
    }

    // Opcional: eliminar un registro de log
    public function destroy($id)
    {
        $log = appointment_access_log::findOrFail($id);
        $log->delete();

        return response()->json(null, 204);
    }
}
