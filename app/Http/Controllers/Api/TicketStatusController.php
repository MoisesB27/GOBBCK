<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TicketStatusRequest; // El Request que está en el Canvas
use App\Models\TicketStatus; // El Modelo que está en el Canvas
use App\Models\Soporte; // Importamos el modelo Soporte para la validación

class TicketStatusController extends Controller
{
    /**
     * Aplica la capa de seguridad.
     */
    public function __construct()
    {
        // Seguridad: Solo 'super-admin' puede gestionar estados de ticket
        // (Tu middleware hasRole('superadmin') en el Request ya maneja esto,
        // pero ponerlo aquí también es una buena doble verificación)
        $this->middleware('auth:sanctum');
    }

    /**
     * Muestra todos los estados de ticket.
     * (CORREGIDO: Se eliminó orderBy('priority_level'))
     */
    public function index()
    {
        // Ordenamos por 'id' (o 'name' si prefieres)
        $statuses = TicketStatus::orderBy('id')->paginate(15);
        return response()->json($statuses);
    }

    /**
     * Muestra un estado de ticket específico.
     */
    public function show($id)
    {
        $status = TicketStatus::findOrFail($id);
        return response()->json($status);
    }

    /**
     * Guarda un nuevo estado de ticket.
     */
    public function store(TicketStatusRequest $request)
    {
        // El TicketStatusRequest (del Canvas) ya se encarga de:
        // 1. Autorizar (solo super-admin)
        // 2. Validar (name, color_code, description, is_active)
        $status = TicketStatus::create($request->validated());
        return response()->json($status, 201);
    }

    /**
     * Actualiza un estado de ticket existente.
     */
    public function update(TicketStatusRequest $request, $id)
    {
        $status = TicketStatus::findOrFail($id);

        // El Request se encarga de validar los datos (incluyendo la regla unique)
        $status->update($request->validated());

        return response()->json($status);
    }

    /**
     * Elimina un estado de ticket.
     * (AÑADIDA: Verificación de seguridad)
     */
    public function destroy($id)
    {
        $status = TicketStatus::findOrFail($id);

        // --- VERIFICACIÓN DE SEGURIDAD ---
        // Contamos si algún ticket en la tabla 'soportes' está usando este estado.
        $ticketsUsandoEsteEstado = Soporte::where('status_id', $status->id)->count();

        if ($ticketsUsandoEsteEstado > 0) {
            return response()->json([
                'message' => 'No se puede eliminar. Hay ' . $ticketsUsandoEsteEstado . ' tickets asociados a este estado.'
            ], 409); // 409 Conflict
        }
        // --- FIN DE VERIFICACIÓN ---

        $status->delete();

        return response()->json(null, 204);
    }
}
