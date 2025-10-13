<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    // Listar usuarios (GET /users)
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }


    // Mostrar usuario individual (GET /users/{user})
    public function show(User $user)
    {
        return response()->json($user);
    }

    // Crear usuario (POST /users)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|string|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json($user, 201);
    }

    // Actualizar usuario (PUT/PATCH /users/{user})
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|string|unique:users,email,' . $user->id,
            'password' => 'sometimes|nullable|string|min:6',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json($user);
    }

    // Eliminar usuario (DELETE /users/{user})
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(null, 204);
    }
}
