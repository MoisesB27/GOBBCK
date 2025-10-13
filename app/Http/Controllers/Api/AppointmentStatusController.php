<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppointmentStatus;
use Illuminate\Http\Request;

class AppointmentStatusController extends Controller
{

    /**
     * Muestra una lista de todos los estados de citas.
     */
    public function index()
    {
        // Se listan por el campo 'order' para mostrar la secuencia lógica de los estados
        $statuses = AppointmentStatus::orderBy('order')->get();
        return response()->json($statuses);
    }

    /**
     * Almacena un nuevo estado de cita.
     */
    public function store(Request $request)
    {
        // Validación de datos: Aseguro que 'name' y 'color_code' sean únicos y requeridos
        $request->validate([
            'name' => 'required|string|max:50|unique:appointment_statuses,name',
            'color_code' => 'required|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', // Código HEX
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $status = AppointmentStatus::create($request->all());

        return response()->json($status, 201);
    }

    /**
     * Muestra un estado de cita específico.
     */
    public function show(AppointmentStatus $status)
    {
        return response()->json($status);
    }

    /**
     * Actualiza un estado de cita existente.
     */
    public function update(Request $request, AppointmentStatus $status)
    {
        $request->validate([
            // Excluimos el estado actual del chequeo de unicidad
            'name' => 'required|string|max:50|unique:appointment_statuses,name,' . $status->id,
            'color_code' => 'required|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $status->update($request->all());

        return response()->json($status);
    }

    /**
     * Elimina un estado de cita.
     */
    public function destroy(AppointmentStatus $status)
    {
        // PRECAUCIÓN: No se debe permitir eliminar un estado si hay citas usándolo.
        if ($status->appointments()->count() > 0) {
            return response()->json([
                'message' => 'No se puede eliminar. Hay citas asociadas a este estado.'
            ], 409); // Código 409: Conflict
        }

        $status->delete();

        return response()->json(null, 204);
    }
}
