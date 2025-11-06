<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth; // Importar Auth para la autorización

class StoreAdminGobRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta petición.
     * Deberíamos restringir esto al rol de "Super Admin".
     */
    public function authorize(): bool
    {
        // Asegúrate de que el rol 'super-admin' exista en tu seeder de roles Spatie.
        return Auth::user() && Auth::user()->hasRole('superadmin');
    }

    /**
     * Obtiene las reglas de validación que aplican al request.
     * Basado en el formulario "Añadir Admin GOB" y las migraciones.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Campos del formulario (image_935223.png)
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'cedula' => 'required|string|max:13|unique:users,cedula', // Único en la tabla users
            'correo_institucional' => 'required|email|max:255|unique:users,email', // Asume que se guarda en la columna 'email' de 'users'
            'telefono' => 'required|string|max:20',

            // Esperamos un ID numérico que exista en la tabla 'instituciones'
            'institucion_id' => 'required|exists:instituciones,id',
        ];
    }

    /**
     * Mensajes de error personalizados en español.
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'apellido.required' => 'El apellido es obligatorio.',
            'cedula.required' => 'La cédula es obligatoria.',
            'cedula.unique' => 'Ya existe un usuario con esta cédula.',
            'correo_institucional.required' => 'El correo es obligatorio.',
            'correo_institucional.email' => 'Debe ser un correo electrónico válido.',
            'correo_institucional.unique' => 'Ya existe un usuario con este correo electrónico.',
            'telefono.required' => 'El teléfono es obligatorio.',

            // Mensajes para la regla de institución
            'institucion_id.required' => 'Debe seleccionar una institución.',
            'institucion_id.exists' => 'La institución seleccionada no es válida.',
        ];
    }
}

