<?php

namespace App\Http\Controllers\Api; // Yo uso este namespace para organizar mis rutas de administración.

use App\Http\Controllers\Controller;
use App\Models\Pgob;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GobPointAdminController extends Controller
{
    /**
     * Muestro la lista de administradores asignados a un Punto GOB específico.
     */
    public function index(Pgob $pgob)
    {
        // Cargo los administradores usando la relación 'admins' que definí en mi modelo Pgob.
        $admins = $pgob->admins;

        // Retorno la lista de administradores en formato JSON.
        return response()->json([
            'pgob' => $pgob->name,
            'administrators' => $admins->map(function ($user) {
                return ['id' => $user->id, 'name' => $user->name, 'email' => $user->email];
            }),
        ]);
    }

    /**
     * Asigno un usuario como administrador a este Punto GOB.
     */
    public function store(Request $request, Pgob $pgob)
    {
        // Valido el ID de usuario y me aseguro de que no esté duplicado para este PGOB.
        $request->validate([
            'user_id' => [
                'required',
                'exists:users,id',
                Rule::unique('gob_point_admins')->where(function ($query) use ($pgob) {
                    return $query->where('pgob_id', $pgob->id);
                }),
            ],
        ], [
            'user_id.unique' => 'Este usuario ya es administrador de este Punto GOB.',
        ]);

        $user = User::findOrFail($request->user_id);

        // Uso el método attach() para crear el registro en mi tabla pivote (gob_point_admins).
        $pgob->admins()->attach($user->id);

        // Pendiente: Aquí implementaré la asignación del rol 'admin-gob' usando mi paquete de permisos.

        return response()->json([
            'message' => 'Administrador ' . $user->name . ' asignado correctamente al Punto GOB: ' . $pgob->name,
        ], 201);
    }

    /**
     * Desvinculo un usuario como administrador de este Punto GOB.
     */
    public function destroy(Pgob $pgob, User $user)
    {
        // Uso detach() para eliminar el registro de mi tabla pivote.
        $pgob->admins()->detach($user->id);

        // Pendiente: Aquí implementaré la revocación del rol si el usuario ya no administra nada.

        return response()->json([
            'message' => 'Administrador ' . $user->name . ' desvinculado correctamente del Punto GOB: ' . $pgob->name,
        ], 204);
    }
}
