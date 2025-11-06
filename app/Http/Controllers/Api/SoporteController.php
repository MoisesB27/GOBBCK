<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Soporte;
use App\Http\Requests\SoporteRequest as StoreSoporteRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class SoporteController extends Controller
{
    /**
     * Muestra la lista de tickets de soporte aplicando filtros de permisos.
     * La seguridad del scope (quién puede ver qué) es la máxima prioridad aquí.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        // Incluimos todas las relaciones necesarias para el dashboard
        $query = Soporte::with(['user', 'pgob', 'status', 'assignedUser', 'priority']);

        // 1. Lógica de Scoping (Delegación de permisos)
        if ($user->hasRole('superadmin')) {
            // Super Admin ve todo.
        }
        else if ($user->hasRole('admin')) {
            // ADMIN GOB: Ve SOLO los tickets de los Puntos GOB que administra.

            // Asumo que el modelo User tiene el método pgobs() o adminPgobs()
            // que devuelve los Pgob a los que está vinculado.
            $pgobIds = $user->adminPgobs()->pluck('id');

            // Filtro de Seguridad: Es el filtro de VITAL IMPORTANCIA.
            $query->whereIn('pgob_id', $pgobIds);

        } else {
            // Usuario normal: Solo ve sus propios tickets.
            $query->where('user_id', $user->id);
        }

        // 2. Filtros Adicionales (Se aplican sobre el scope de seguridad)
        if ($request->has('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }
        if ($request->has('pgob_id')) {
            $query->where('pgob_id', $request->input('pgob_id'));
        }
        if ($request->has('status_id')) {
            $query->where('status_id', $request->input('status_id'));
        }
        if ($request->has('priority_id')) {
            $query->where('priority_id', $request->input('priority_id'));
        }


        $soportes = $query->latest()->paginate(15);
        return response()->json($soportes);
    }

    /**
     * Muestra un ticket específico, asegurando que el usuario tenga permiso.
     */
    public function show($id)
    {
        $soporte = Soporte::with(['user', 'pgob', 'status', 'assignedUser', 'priority'])->findOrFail($id);
        $user = Auth::user();

        // Verificación de Acceso Estricta
        if (!$user->hasRole('superadmin') && $user->id !== $soporte->user_id) {

            if ($user->hasRole('admin')) {
                // Si es admin, debe administrar el PGOBS del ticket
                $pgobIds = $user->adminPgobs()->pluck('id');

                if (!$pgobIds->contains($soporte->pgob_id)) {
                    abort(403, 'No tienes permiso para ver este ticket de otro Punto GOB.');
                }
            } else {
                // Si es un usuario normal que no creó el ticket, denegamos.
                abort(403, 'No tienes permiso para ver este ticket.');
            }
        }

        return response()->json($soporte);
    }

    /**
     * Guarda un nuevo ticket de soporte.
     */
    public function store(StoreSoporteRequest $request)
    {
        $validated = $request->validated();

        try {
            $soporte = Soporte::create($validated);
            Log::info("Ticket de soporte creado, ID: {$soporte->id}.");
            return response()->json($soporte, 201);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error al crear ticket de soporte: ' . $e->getMessage());
            return response()->json(['message' => 'Error al crear el ticket.'], 500);
        }
    }

    /**
     * Actualiza un ticket existente (Asignación, cambio de estado, etc.).
     */
    public function update(StoreSoporteRequest $request, $id)
    {
        $soporte = Soporte::findOrFail($id);
        $user = Auth::user();

        // Lógica de Autorización: Si no es superadmin, comprobamos si es el admin del PGOBS.
        if (!$user->hasRole('superadmin')) {

            if ($user->hasRole('admin')) {
                $pgobIds = $user->adminPgobs()->pluck('id');

               // Denegamos si el ticket NO pertenece a los PGOBS que administra.
                if (!$pgobIds->contains($soporte->pgob_id)) {
                    abort(403, 'No tienes permiso para actualizar un ticket de otro Punto GOB.');
                }
            } else {
                // La ruta ya protege esto, pero mantenemos esta capa por seguridad.
                abort(403, 'Solo un administrador puede actualizar tickets de soporte.');
            }
        }

        try {
            $soporte->update($request->validated());
            Log::info("Ticket #{$soporte->id} actualizado por Admin ID: " . $user->id);
            return response()->json($soporte->load(['status', 'priority', 'assignedUser']));
        } catch (\Exception $e) {
            Log::error('Error al actualizar ticket de soporte: ' . $e->getMessage());
            return response()->json(['message' => 'Error al actualizar el ticket.'], 500);
        }
    }

    /**
     * Elimina un ticket de soporte.
     */
    public function destroy($id)
    {
        $soporte = Soporte::findOrFail($id);
        $user = Auth::user();

        // Lógica de Autorización: Si no es superadmin, comprobamos si es el admin del PGOBS.
        if (!$user->hasRole('superadmin')) {

            if ($user->hasRole('admin')) {
                $pgobIds = $user->adminPgobs()->pluck('id');

               // Denegamos si el ticket NO pertenece a los PGOBS que administra.
                if (!$pgobIds->contains($soporte->pgob_id)) {
                    abort(403, 'No tienes permiso para eliminar un ticket de otro Punto GOB.');
                }
            } else {
                // Denegamos si es un rol 'usuario'.
                abort(403, 'Solo un administrador puede eliminar tickets de soporte.');
            }
        }

        try {
            $soporte->delete();
            Log::info("Ticket #{$soporte->id} eliminado por Admin ID: " . $user->id);
            return response()->json(null, 204);
        } catch (\Exception $e) {
            Log::error('Error al eliminar ticket de soporte: ' . $e->getMessage());
            return response()->json(['message' => 'Error al eliminar el ticket.'], 500);
        }
    }
}
