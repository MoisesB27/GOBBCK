<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth; // Necesario para la autorización

class UserController extends Controller
{
    /**
     * Muestro y listo usuarios con paginación, filtros y eager loading de roles/perfil.
     */
    public function index(Request $request)
    {
        // 1. Eager Loading: Cargo el perfil y los roles para evitar N+1
        $query = User::with('profile', 'roles');

        // 2. Aplicar búsqueda por nombre o email
        if ($request->has('search')) {
            $searchTerm = '%' . $request->input('search') . '%';
            $query->where('name', 'like', $searchTerm)->orWhere('email', 'like', $searchTerm);
        }

        // 3. Aplicar filtro por rol (usa el método 'role' del paquete Spatie)
        if ($request->has('role')) {
            $query->role($request->input('role'));
        }

        // 4. Paginar y ejecutar
        $users = $query->paginate(15);
        return response()->json($users);
    }


    /**
     * Muestro un usuario individual con sus relaciones.
     */
    public function show(User $user)
    {
        // Eager load el perfil, los roles y los Puntos GOB que administra
        $user->load('profile', 'roles', 'adminPgobs');
        return response()->json($user);
    }

    /**
     * Creo un nuevo usuario y le asigno roles.
     */
    public function store(Request $request)
    {
        // 1. Validación de datos (incluyendo roles)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|string|unique:users,email',
            'password' => 'required|string|min:6',
            // Campo opcional para asignar roles al crear
            'roles' => 'sometimes|array',
            'roles.*' => 'string|exists:roles,name', // Valida que el rol exista
        ]);

        // 2. Hash del password
        $validated['password'] = Hash::make($validated['password']);

        // 3. Crear usuario
        $user = User::create($validated);

        // 4. Asignar roles si se proporcionan
        if (isset($validated['roles'])) {
            $user->assignRole($validated['roles']);
            // Recargo para que la respuesta incluya los roles asignados
            $user->load('roles');
        }

        return response()->json($user, 201);
    }

    /**
     * Actualizo un usuario existente y sincronizo sus roles.
     */
    public function update(Request $request, User $user)
    {
        $authUser = Auth::user();

        if ($authUser->id !== $user->id && !$authUser->hasRole('super-admin')) {
            return response()->json(['message' => 'No autorizado para editar este usuario.'], 403);
        }

        // 1. Validación de datos
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|string|unique:users,email,' . $user->id,
            'password' => 'sometimes|nullable|string|min:6',
            'roles' => 'sometimes|array',
            'roles.*' => 'string|exists:roles,name',
        ]);

        // 2. Manejo de password
        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // 3. Sincronizar roles (solo si se proporciona el campo 'roles' y el Auth::user() es admin)
        if (isset($validated['roles']) && $authUser->hasRole('super-admin')) {
            // syncRoles reemplaza todos los roles existentes con la nueva lista
            $user->syncRoles($validated['roles']);
            // Remuevo 'roles' de validated para que no intente actualizar un campo inexistente en la tabla 'users'
            unset($validated['roles']);
        }

        // 4. Actualizar usuario
        $user->update($validated);

        // 5. Devolver usuario actualizado (con roles)
        $user->load('profile', 'roles');

        return response()->json($user);
    }

    /**
     * Elimino un usuario.
     */
    public function destroy(User $user)
    {
        // Autorización: Solo 'super-admin' puede eliminar
        $authUser = Auth::user();
        if (!$authUser->hasRole('super-admin')) {
            return response()->json(['message' => 'No autorizado para eliminar usuarios.'], 403);
        }

        $user->delete();
        return response()->json(null, 204);
    }
}
