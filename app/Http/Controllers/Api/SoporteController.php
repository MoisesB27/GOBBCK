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
     * Muestra la lista de tickets de soporte aplicando filtros de permisos.
     * * - 'superadmin': Ve todos los tickets.
     * - 'admin': Ve solo los tickets de los Puntos GOB que administra.
     * - 'usuario': Ve solo los tickets que él mismo creó.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Soporte::with(['user', 'pgob', 'status']);

        // 1. Lógica de Scoping (Delegación de permisos)
        if ($user->hasRole('superadmin')) {
            // El Super Admin ve todo, no se aplica filtro.
        }
        else if ($user->hasRole('admin')) {
            // Si es un Admin (delegado de una institución), filtramos por los Puntos GOB que administra.

            // Obtenemos los IDs de los Puntos GOB relacionados con el admin.
            // Asumimos que el método adminPgobs() existe en el modelo User y retorna los PGOBS relacionados.
            $pgobIds = $user->adminPgobs()->pluck('id');

            // Solo mostramos los tickets que pertenecen a esos Puntos GOB.
            $query->whereIn('pgob_id', $pgobIds);

        } else {
            // Si es un usuario normal (rol 'usuario'), solo ve los tickets que creó.
            $query->where('user_id', $user->id);
        }

        // 2. Filtros Adicionales (se aplican después del scoping)
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
     * Muestra un ticket específico, asegurando que el usuario tenga permiso para verlo.
     * Solo lo ve: el creador, el superadmin o el admin del PGOBS asociado.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $soporte = Soporte::with(['user', 'pgob', 'status'])->findOrFail($id);
        $user = Auth::user();

        // Si NO es el creador del ticket y NO es superadmin, revisamos la delegación.
        if ($user->id !== $soporte->user_id && !$user->hasRole('superadmin')) {

            if ($user->hasRole('admin')) {
                 // Es admin, comprobamos que administre el PGOBS al que pertenece el ticket.
                    $pgobIds = $user->adminPgobs()->pluck('id');

                 // Si el PGOBS del ticket no está en su lista de PGOBS administrados, se deniega.
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
     * Asocia automáticamente el ticket al usuario autenticado.
     *
     * @param \App\Http\Requests\SoporteRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreSoporteRequest $request)
    {
        // Sobrescribimos el user_id con el ID del usuario autenticado por seguridad.
        $validated = $request->validated();
        $validated['user_id'] = Auth::id();

        $soporte = Soporte::create($validated);
        return response()->json($soporte, 201);
    }

    /**
     * Actualiza un ticket existente.
     * Solo permitido para superadmin o admin del PGOBS asociado (protegido también por la ruta).
     *
     * @param \App\Http\Requests\SoporteRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
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

        $soporte->update($request->validated());
        return response()->json($soporte);
    }

    /**
     * Elimina un ticket de soporte.
     * Solo permitido para superadmin o admin del PGOBS asociado.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
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

        $soporte->delete();
        return response()->json(null, 204);
    }
}
