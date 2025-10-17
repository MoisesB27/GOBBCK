<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class ProfileController extends Controller
{
    public function show ()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['error' => 'Usuario no autenticado'], 401);
            }

            return response()->json($user);

        } catch (\Exception $e) {

            Log::warning('Error al obtener el perfil del usuario: ' . $e->getMessage());

            return response()->json(['error' => 'Error al obtener el perfil del usuario'], 500);
        }
    }

    public function update ()
    {
        try {
    $user = Auth::user();

    if (!$user) {
        return response()->json(['error' => 'Usuario no autenticado'], 401);
    }

    $user = \App\Models\User::find($user->id);

    $data = request()->only(['name', 'cedula', 'email', 'password']);

    if (isset($data['password'])) {
        $data['password'] = bcrypt($data['password']);
    } else {
        unset($data['password']);
    }

    $user->update($data);

    return response()->json($user);

} catch (\Exception $e) {
    Log::warning('Error al actualizar el perfil del usuario: ' . $e->getMessage());

    return response()->json(['error' => 'Error al actualizar el perfil del usuario'], 500);
}
    }
}
