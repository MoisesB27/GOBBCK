<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    // Registro
    public function register(RegisterRequest $request)
{
    //  Verificar que llega al método
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:8|confirmed',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'debug_step' => '1 - VALIDATION FAILED',
            'errors' => $validator->errors()
        ], 422);
    }

    try {

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Asignar el rol 'usuario' al usuario recién creado
        $user->assignRole('usuario');

         // Generar token de autenticación
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Usuario registrado exitosamente',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ],
            'access_token' => $token,
            'token_type' => 'Bearer'
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => 'Error al registrar usuario: ' . $e->getMessage()
        ], 500);}

    }

    // Login
        public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales son incorrectas.'],
            ]);
        }

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token'=> $token,
        ]);
    }



    // Logout
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Sesión cerrada correctamente']);
    }


    // Cambiar contraseña
    public function changePassword(ChangePasswordRequest $request)
    {
        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['La contraseña actual es incorrecta.'],
            ]);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Contraseña cambiada correctamente.']);
    }
    }
