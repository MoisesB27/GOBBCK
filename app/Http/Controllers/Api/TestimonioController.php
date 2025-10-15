<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TestimonioRequest as StoreTestimonioRequest;
use App\Models\Testimonio;
use Illuminate\Support\Facades\Auth; // Importamos Auth para obtener el usuario autenticado

/**
 * Gestiona las operaciones CRUD para los testimonios (comentarios de experiencia) de los usuarios.
 * Se implementa lógica de autorización a nivel de controlador para las acciones de edición/eliminación.
 */
class TestimonioController extends Controller
{
    /**
     * Constructor del controlador.
     * Aunque no se usa aquí, es donde se aplica el middleware de autorización con Policies si se usa ese método.
     * Por ahora, la seguridad inicial se maneja en routes/api.php.
     */
    public function __construct()
    {
        // Nota: Las rutas de 'store' están aseguradas con 'auth:sanctum'.
        // Las de 'update'/'destroy' están protegidas por 'role:super-admin' en las rutas.
        // La capa adicional de validación de dueño está en los métodos.
    }

    /**
     * Muestra una lista paginada de todos los testimonios.
     * Esta ruta debe ser pública, permitiendo a cualquiera ver los comentarios.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Usamos 'with('user')' para el Eager Loading y evitar el problema N+1.
        // El frontend necesita saber quién dejó el testimonio.
        $testimonios = Testimonio::with('user')->paginate(15);
        return response()->json($testimonios);
    }

    /**
     * Muestra un testimonio específico por ID.
     * Esta ruta también es pública.
     *
     * @param int $id ID del testimonio.
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // Se usa findOrFail para devolver un 404 si el testimonio no existe.
        $testimonio = Testimonio::with('user')->findOrFail($id);
        return response()->json($testimonio);
    }

    /**
     * Almacena un nuevo testimonio.
     * Esta ruta está protegida por 'auth:sanctum', asegurando que solo usuarios logueados puedan comentar.
     *
     * @param StoreTestimonioRequest $request Petición validada con los datos del testimonio.
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreTestimonioRequest $request)
    {
        $validatedData = $request->validated();

        // Seguridad: Asignamos el testimonio directamente al usuario autenticado.
        // Esto evita que un usuario intente enviar un 'user_id' diferente.
        $validatedData['user_id'] = Auth::id();

        $testimonio = Testimonio::create($validatedData);
        // Devolvemos 201 Created estándar para la creación de recursos.
        return response()->json($testimonio, 201);
    }

    /**
     * Actualiza un testimonio existente.
     * Esta ruta está protegida por 'role:super-admin' en las rutas.
     *
     * @param StoreTestimonioRequest $request Petición validada con los datos a actualizar.
     * @param int $id ID del testimonio a actualizar.
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(StoreTestimonioRequest $request, $id)
    {
        $testimonio = Testimonio::findOrFail($id);
        $authUser = Auth::user();

        //  Autorización interna: Doble verificación de seguridad.
        // Solo permitimos la actualización si:
        // 1. El ID del usuario autenticado coincide con el 'user_id' del testimonio (es el dueño).
        // 2. O, el usuario autenticado tiene el rol 'super-admin'.
        if ($authUser->id !== $testimonio->user_id && !$authUser->hasRole('super-admin')) {
            // Devolvemos 403 Forbidden si no se cumplen las condiciones.
            return response()->json(['message' => 'No autorizado para editar este testimonio.'], 403);
        }

        $testimonio->update($request->validated());
        return response()->json($testimonio);
    }

    /**
     * Elimina un testimonio.
     * Esta ruta está protegida por 'role:super-admin' en las rutas.
     *
     * @param int $id ID del testimonio a eliminar.
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $testimonio = Testimonio::findOrFail($id);
        $authUser = Auth::user();

        // Autorización interna: Doble verificación de seguridad (mismo que update).
        // Solo se permite la eliminación si es el dueño O un 'super-admin'.
        if ($authUser->id !== $testimonio->user_id && !$authUser->hasRole('super-admin')) {
            return response()->json(['message' => 'No autorizado para eliminar este testimonio.'], 403);
        }

        $testimonio->delete();
        // Devolvemos 204 No Content estándar para una eliminación exitosa sin cuerpo de respuesta.
        return response()->json(null, 204);
    }
}
