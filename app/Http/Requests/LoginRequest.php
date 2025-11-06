<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta petición.
     */
    public function authorize()
    {
        // El login es siempre público
        return true;
    }

    /**
     * Obtiene las reglas de validación que se aplican a la solicitud.
     */
    public function rules()
    {
        return [
            // Eliminamos 'exists'. La función Auth::attempt() se encargará
            // de verificar si el usuario existe y si la contraseña es correcta.
            'email' => 'required|email|string',
            'password' => 'required|string',
        ];
    }

    /**
     * Obtiene los mensajes de error.
     */
    public function messages()
    {
        return [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Debe ser una dirección de correo válida.',
            // El mensaje 'email.exists' ya no es necesario
            'password.required' => 'La contraseña es obligatoria.',
        ];
    }

    /**
     * Maneja una validación fallida y devuelve una respuesta HTTP personalizada.
     * Esto garantiza que los errores de validación se devuelvan siempre como JSON con código 422.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Error de validación. Por favor, revise los datos proporcionados.',
            'errors' => $validator->errors()
        ], 422));
    }
}
