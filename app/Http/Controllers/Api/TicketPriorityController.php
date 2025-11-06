<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TicketPriorityRequest;
use App\Models\TicketPriority;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class TicketPriorityController extends Controller
{
    /**
     * Aplica la capa de seguridad.
     */
    public function __construct()
    {
        // Seguridad: Solo usuarios con rol 'superadmin' pueden acceder a esta data maestra
        $this->middleware(['role:superadmin']);
    }

    /**
     * Muestro todos los estados de ticket, ordenados y paginados.
     */
    public function index()
    {
        try {
            // Cargo y ordeno las prioridades.
            $priorities = TicketPriority::orderBy('order')->paginate(15);
            return response()->json($priorities);
        } catch (\Exception $e) {
            Log::error('Error al listar prioridades: ' . $e->getMessage());
            return response()->json(['message' => 'Error al obtener la lista de prioridades.'], 500);
        }
    }

    /**
     * Muestro una prioridad de ticket específica.
     */
    public function show($id)
    {
        $priority = TicketPriority::findOrFail($id);
        return response()->json($priority);
    }

    /**
     * Guardo una nueva prioridad de ticket.
     */
    public function store(TicketPriorityRequest $request)
    {
        try {
            // Utilizo el Request Form para validar y crear.
            $priority = TicketPriority::create($request->validated());
            return response()->json($priority, 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error al guardar prioridad: ' . $e->getMessage());
            return response()->json(['message' => 'Error al guardar la prioridad.'], 500);
        }
    }

    /**
     * Actualizo una prioridad de ticket existente.
     */
    public function update(TicketPriorityRequest $request, $id)
    {
        try {
            $priority = TicketPriority::findOrFail($id);
            $priority->update($request->validated());
            return response()->json($priority);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error al actualizar prioridad: ' . $e->getMessage());
            return response()->json(['message' => 'Error al actualizar la prioridad.'], 500);
        }
    }

    /**
     * Elimino una prioridad de ticket.
     */
    public function destroy($id)
    {
        $priority = TicketPriority::findOrFail($id);

        // PRECAUCIÓN: No se debe permitir eliminar si hay tickets usándolo.
        // Asumiendo que el modelo TicketPriority tiene la relación hasMany Soporte:
        if ($priority->soportes()->count() > 0) {
            return response()->json([
                'message' => 'No se puede eliminar. Hay tickets asociados a esta prioridad.'
            ], 409); // Código 409: Conflict
        }

        $priority->delete();
        return response()->json(null, 204);
    }
}
