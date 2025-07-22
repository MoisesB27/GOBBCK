<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Requests\appointmentsRequest as StoreAppointmentRequest;
use App\Models\appointments;
use Illuminate\Http\Request;

class AppointmentsController extends Controller
{
    // Listar citas (opcionalmente filtrar por usuario o estado)
    public function index(Request $request)
    {
        $query = appointments::with(['user', 'assignedUser', 'service', 'pgob']);

        // Filtros opcionales
        if ($request->has('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        $appointments = $query->paginate(15);

        return response()->json($appointments);
    }

    // Mostrar una cita específica
    public function show($id)
    {
        $appointment = appointments::with(['user', 'assignedUser', 'service', 'pgob'])->findOrFail($id);

        return response()->json($appointment);
    }

    // Crear nueva cita
    public function store(StoreAppointmentRequest $request)
    {
        $appointment = appointments::create($request->validated());

        return response()->json($appointment, 201);
    }

    // Actualizar una cita
    public function update(StoreAppointmentRequest $request, $id)
    {
        $appointment = appointments::findOrFail($id);
        $appointment->update($request->validated());

        return response()->json($appointment);
    }

    // Eliminar una cita
    public function destroy($id)
    {
        $appointment = appointments::findOrFail($id);
        $appointment->delete();

        return response()->json(null, 204);
    }
}
