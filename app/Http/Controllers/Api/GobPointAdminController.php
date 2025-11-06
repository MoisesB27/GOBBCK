<?php

namespace App\Http\Controllers\Api; // Tu namespace de Admin

use App\Http\Controllers\Controller;
use App\Models\Pgob;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Admin\StoreAdminGobRequest; // ¡Usamos el Request del Canvas!


class GobPointAdminController extends Controller
{
    /**
     * Muestro la lista de administradores asignados a un Punto GOB específico.
     * (Este método tuyo está perfecto)
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
     * CREA un nuevo usuario (basado en el formulario) y
     * LO ASIGNA como administrador a este Punto GOB.
     * (Este es el método corregido)
     */
    public function store(StoreAdminGobRequest $request, Pgob $pgob)
    {
        // $request->validated() nos da los datos limpios gracias al StoreAdminGobRequest
        $validatedData = $request->validated();

        try {
            DB::beginTransaction();

            // 1. Crear el Usuario
            $user = User::create([
                'name' => $validatedData['nombre'], // 'name' es el campo por defecto de Laravel
                'apellido' => $validatedData['apellido'], // Llenamos el campo de la migración
                'email' => $validatedData['correo_institucional'], // Usamos la columna 'email'
                'cedula' => $validatedData['cedula'],     // Llenamos el campo de la migración
                'telefono' => $validatedData['telefono'],   // Llenamos el campo de la migración
                'institucion_id' => $validatedData['institucion_id'], // Llenamos el campo de la migración
                'password' => Hash::make($validatedData['cedula']), // Usamos la cédula como contraseña inicial
            ]);

            // 2. Asignarle el Rol
            // (Asegúrate de que 'admin-gob' exista en tu seeder de roles Spatie)
            $user->assignRole('admin');

            // 3. Vincularlo al Punto GOB
            // (Usa la relación 'admins' que corregimos en Pgob.php)
            $pgob->admins()->attach($user->id);

            DB::commit();

            return response()->json([
                'message' => 'Administrador ' . $user->name . ' creado y asignado correctamente al Punto GOB: ' . $pgob->name,
                'user' => $user
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear Admin GOB: ' . $e->getMessage());
            return response()->json(['message' => 'Error al crear el administrador.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Desvinculo un usuario como administrador de este Punto GOB.
     * (¡ACTUALIZADO CON LA NUEVA LÓGICA!)
     */
    public function destroy(Pgob $pgob, User $user)
    {
        try {

            $pgob->admins()->detach($user->id);

            // Verifico si este usuario sigue administrando OTROS Puntos GOB.
            // Usamos ->load('pgobs') para recargar la relación después de desvincular.
            // La relación 'pgobs' la definiste en tu User.php
            if ($user->load('pgobs')->pgobs->count() == 0) {
                // Si ya no administra ninguno, le quito el rol.
                $user->removeRole('admin'); // Usando 'admin' (basado en tu seeder)
                Log::info('Rol "admin" revocado al usuario: ' . $user->name . ' (ID: ' . $user->id . ') por no tener más Puntos GOB asignados.');
            }

            return response()->json([
                'message' => 'Administrador ' . $user->name . ' desvinculado correctamente del Punto GOB: ' . $pgob->name,
            ], 200); // 200 OK

        } catch (\Exception $e) {
            Log::error('Error al desvincular Admin GOB: ' . $e->getMessage());
            return response()->json(['message' => 'Error al desvincular el administrador.'], 500);
        }
    }
}

