<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ChangePasswordRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar esta solicitud.
     */
    public function authorize(): bool
    {
        // Solo usuarios autenticados pueden cambiar su contraseña
        return Auth::check();
    }

    /**
     * Obtiene las reglas de validación que se aplican a la solicitud.
     */
    public function rules(): array
    {
        return [
            // 1. La contraseña actual es requerida
            'current_password' => 'required',

            // 2. La nueva contraseña es requerida y debe ser confirmada (coincidencia)
            'new_password' => 'required|string|min:8|confirmed',

            // 3. Este campo (new_password_confirmation) es el que hace la coincidencia
            // Nota: Ya no necesita 'min:8' aquí, lo toma de 'new_password'
            'new_password_confirmation' => 'required|string',
        ];
    }

    /**
     * Lógica de Negocio: Verifica que la contraseña actual sea la correcta.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $user = Auth::user();

            // Verificamos si la contraseña que el usuario envió (current_password)
            // coincide con el hash guardado en la base de datos ($user->password).
            if (!Hash::check($this->current_password, $user->password)) {
                // Si NO coincide, agregamos un error de validación personalizado.
                $validator->errors()->add('current_password', 'La contraseña actual ingresada es incorrecta.');
            }
        });
    }

    /**
     * Mensajes de error personalizados.
     */
    public function messages(): array
    {
        return [
            'current_password.required' => 'Debe ingresar su contraseña actual.',
            'new_password.required' => 'La nueva contraseña es obligatoria.',
            'new_password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            // Este mensaje se activa si 'new_password' y 'new_password_confirmation' no coinciden.
            'new_password.confirmed' => 'La nueva contraseña y la confirmación no coinciden.'
        ];
    }
}
