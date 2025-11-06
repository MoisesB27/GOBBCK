<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; // 1. Importar la clase Request
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // 2. Importar el Facade de Hash
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator; // 3. Importar el Validador
use Illuminate\Validation\Rule; // 4. Importar 'Rule' para validación unique
use App\Models\User;

class ProfileController extends Controller
{
    public function show()
    {
        try {
            $authUser = Auth::user(); // 1. Obtenemos el usuario de Auth (puede no ser Eloquent)

            if (!$authUser) {
                return response()->json(['error' => 'Usuario no autenticado'], 401);
            }

            $user = User::find($authUser->id);

            if (!$user) {
                return response()->json(['error' => 'Usuario no encontrado'], 404);
            }

            // 3. ¡Ahora $user SÍ es un modelo Eloquent!
            $user->load('profile');

            return response()->json($user);

        } catch (\Exception $e) {
            Log::warning('Error al obtener el perfil del usuario: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener el perfil del usuario'], 500);
        }
    }

    /**
     * Actualiza el perfil del usuario autenticado.
     */
    public function update(Request $request)
    {
        try {
            $authUser = Auth::user();

            if (!$authUser) {
                return response()->json(['error' => 'Usuario no autenticado'], 401);
            }

            $user = User::find($authUser->id);

            // --- ¡¡ESTA ES LA LÍNEA QUE FALTA!! ---
            // Debemos verificar que ENCONTRAMOS al usuario antes de usarlo.
            if (!$user) {
                Log::warning("Usuario autenticado (ID: $authUser->id) no encontrado en la tabla users.");
                return response()->json(['error' => 'Registro de usuario no encontrado'], 404);
            }
            // --- Fin de la nueva verificación ---

            // Si llegamos aquí, $user SÍ es un modelo Eloquent y no es null.

            $validator = Validator::make($request->all(), [
                'name'     => ['sometimes', 'required', 'string', 'max:255'],
                'cedula'   => ['sometimes', 'required', 'string', Rule::unique('users')->ignore($user->id)],
                'email'    => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
                'password' => ['sometimes', 'required', 'string', 'min:8'],

                'first_name' => ['sometimes', 'required', 'string', 'max:255'],
                'last_name'  => ['sometimes', 'required', 'string', 'max:255'],
                'sexo'       => ['sometimes', 'required', 'string', 'max:10'],
                'direccion'  => ['sometimes', 'nullable', 'string'],
                'phone'      => ['sometimes', 'nullable', 'string', 'max:20'],
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $validatedData = $validator->validated();

            // --- Actualizar Usuario ---
            $userData = array_filter($validatedData, function ($key) {
                return in_array($key, ['name', 'email', 'cedula']);
            }, ARRAY_FILTER_USE_KEY);

            if (!empty($userData)) {
                $user->update($userData);
            }

            // --- Actualizar Password ---
            if (isset($validatedData['password'])) {
                $user->update([
                    'password' => Hash::make($validatedData['password'])
                ]);
            }

            // --- Actualizar Perfil ---
            $profileData = array_filter($validatedData, function ($key) {
                return in_array($key, ['first_name', 'last_name', 'sexo', 'direccion', 'phone']);
            }, ARRAY_FILTER_USE_KEY);

            if (!empty($profileData)) {
                $user->profile()->updateOrCreate(
                    ['user_id' => $user->id],
                    $profileData
                );
            }

            $user->load('profile');

            return response()->json([
                'message' => 'Perfil actualizado correctamente',
                'user' => $user,
            ]);

        } catch (\Exception $e) {
            // Revisa el log para ver el mensaje exacto
            Log::error('Error actualizando perfil usuario: ' . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar el perfil del usuario'], 500);
        }
    }

}
