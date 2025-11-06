<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon; // Importamos Carbon para el cálculo de fechas

class UserController extends Controller
{
    /**
     * Muestro y listo usuarios con paginación, filtros y eager loading de roles/perfil.
     */
    public function index(Request $request)
    {
        // --- 0. CÁLCULO DE CONTADORES DEL DASHBOARD ---
        $today = Carbon::today();
        $thirtyDaysAgo = Carbon::today()->subDays(30);

        $counts = [
            'total_usuarios' => User::count(),
            // Asumo que 'Usuarios nuevos' son aquellos registrados en los últimos 30 días
            'usuarios_nuevos' => User::where('created_at', '>=', $thirtyDaysAgo)->count(),
            // Asumo que 'Usuarios activos' son todos aquellos que tienen un rol asignado y no están inhabilitados
            // Nota: Para inhabilitar, asumimos la tabla 'users' tiene un campo 'is_active'
            'usuarios_activos' => User::where('is_active', true)->count(),

            'usuarios_inhabilitados' => User::where('is_active', false)->count(),
        ];

        // 1. Eager Loading: Cargamos roles y los Puntos GOB que administra el usuario
        // Nota: Asumo que la relación 'adminPgobs' está definida en el modelo User
        $query = User::with('roles', 'adminPgobs');

        // 2. Aplicar búsqueda por nombre, email o cédula (Añadido 'apellido' y 'cedula')
        if ($request->has('search')) {
            $searchTerm = '%' . $request->input('search') . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                    ->orWhere('apellido', 'like', $searchTerm)
                    ->orWhere('email', 'like', $searchTerm)
                    ->orWhere('cedula', 'like', $searchTerm);
            });
        }

        // 3. Aplicar filtro por rol (usa el método 'role' del paquete Spatie)
        if ($request->has('role')) {
            $query->role($request->input('role'));
        }

        // 4. Paginar y ejecutar
        $users = $query->paginate(15);

        // 5. Devolver la respuesta con los contadores
        return response()->json([
            'counts' => $counts,
            'users' => $users
        ]);
    }


    /**
     * Muestro un usuario individual con sus relaciones.
     */
    public function show(User $user)
    {
        // Eager load roles y los Puntos GOB que administra
        $user->load('roles', 'adminPgobs', 'institucion');
        return response()->json($user);
    }

    /**
     * Creo un nuevo usuario y le asigno roles.
     * Este método se usa para crear ciudadanos y administradores (si se envían los campos admin).
     */
    public function store(Request $request)
    {
        // 1. Validación de datos (usamos Request básico, puedes cambiarlo por un FormRequest si es complejo)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'apellido' => 'required|string|max:255', // Añadido
            'cedula' => 'nullable|string|max:13|unique:users,cedula', // Añadido
            'telefono' => 'nullable|string|max:20', // Añadido
            'institucion_id' => 'nullable|exists:instituciones,id', // Añadido
            'email' => 'required|email|string|unique:users,email',
            'password' => 'required|string|min:6',
            'roles' => 'nullable|array', // Array de nombres de roles
            'roles.*' => 'string|exists:roles,name', // Valida que los roles existan
        ]);

        // 2. Hash del password
        $validated['password'] = Hash::make($validated['password']);

        // 3. Crear usuario
        $user = User::create($validated);

        // 4. Asignar roles si se proporcionan
        if (isset($validated['roles'])) {
            $user->syncRoles($validated['roles']); // syncRoles en lugar de assignRole si se envían múltiples
        } else {
             // Asigna el rol por defecto si no se especifica (ej. 'usuario' o 'ciudadano')
            $user->assignRole('usuario');
        }

        // 5. Devolver usuario creado (con roles)
        $user->load('roles', 'institucion');
        return response()->json($user, 201);
    }

    /**
     * Actualizo un usuario existente y sincronizo sus roles.
     */
    public function update(Request $request, User $user)
    {
        $authUser = Auth::user();

        // 0. Autorización: Permite al usuario editar su propio perfil o al superadmin editar a cualquiera.
        if ($authUser->id !== $user->id && !$authUser->hasRole('superadmin')) {
            return response()->json(['message' => 'No autorizado para editar este usuario.'], 403);
        }

        // 1. Validación de datos (añadido validación para campos de admin)
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'apellido' => 'sometimes|nullable|string|max:255', // Añadido
            'cedula' => ['sometimes', 'nullable', 'string', 'max:13', Rule::unique('users', 'cedula')->ignore($user->id)], // Añadido
            'telefono' => 'sometimes|nullable|string|max:20', // Añadido
            'institucion_id' => 'sometimes|nullable|exists:instituciones,id', // Añadido
            'email' => ['sometimes', 'required', 'email', 'string', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'sometimes|nullable|string|min:6',
            'roles' => 'sometimes|array',
            'roles.*' => 'string|exists:roles,name',
            'is_active' => 'sometimes|boolean', // Añadido para inhabilitar/habilitar
        ]);

        // 2. Manejo de password
        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // 3. Sincronizar roles (SOLO si se proporciona el campo 'roles' y el Auth::user() es superadmin)
        if (isset($validated['roles']) && $authUser->hasRole('superadmin')) {
            $user->syncRoles($validated['roles']);
            unset($validated['roles']);
        }

        // 4. Actualizar usuario
        $user->update($validated);

        // 5. Devolver usuario actualizado (con roles)
        $user->load('roles', 'institucion');
        return response()->json($user);
    }

    /**
     * Elimino un usuario.
     */
    public function destroy(User $user)
    {
        // Autorización: Solo 'superadmin' puede eliminar
        $authUser = Auth::user();
        if (!$authUser->hasRole('superadmin')) {
            return response()->json(['message' => 'No autorizado para eliminar usuarios.'], 403);
        }

        $user->delete();
        return response()->json(null, 204);
    }
}
