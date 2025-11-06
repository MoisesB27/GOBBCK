<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule; // Necesario para la validación de unicidad de email

class UserUpdateRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta petición.
     * En el perfil, siempre es 'true' porque el usuario solo se actualiza a sí mismo.
     */
    public function authorize(): bool
    {
        // Solo permitimos que el usuario actualice su propio perfil.
        // Dado que la ruta está bajo 'auth:sanctum', el usuario está autenticado.
        return true;
    }

    /**
     * Obtiene las reglas de validación que se aplican a la petición (PUT/PATCH /api/profile).
     */
    public function rules(): array
    {
        // Obtenemos el ID del usuario autenticado para la regla 'unique'
        $userId = Auth::id();

        return [
            // Campos obligatorios y básicos
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'max:100'],
            'apellido' => ['nullable', 'string', 'max:100'],
            'telefono' => ['nullable', 'string', 'max:15'],
            'institucion_id' => ['nullable', 'exists:instituciones,id'],
            'active' => ['nullable', 'boolean'],

            // Validación de Email: Debe ser único en la tabla 'users',
            // pero excluyendo el email actual del usuario que está actualizando.
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],

            // Campos de perfil específicos
                'cedula' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('users', 'cedula')->ignore($userId),
            ],

            // Si el password se envía, debe ser validado.
            // La clave 'password' solo es requerida si se incluye en la petición.
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];
    }

    /**
     * Personaliza los mensajes de error (Opcional, pero recomendado).
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El campo Nombres es obligatorio.',
            'email.unique' => 'Este correo electrónico ya está registrado por otro usuario.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
        ];
    }
}
