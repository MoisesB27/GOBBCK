<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

// Renombramos el request para que coincida con el controlador
// (Si tu controlador usa 'InstitucionesRequest', renombra la clase a 'InstitucionesRequest')
class InstitucionesRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta solicitud.
     * Solo el 'superadmin' puede crear/actualizar instituciones.
     */
    public function authorize(): bool
    {
        // Usamos el rol 'superadmin' que confirmaste
        return Auth::check() && Auth::user()->hasRole('superadmin');
    }

    /**
     * Obtiene las reglas de validación que se aplican a la solicitud.
     * Valida todos los campos del formulario "Editar detalles de institución".
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        // El 'id' a ignorar en 'unique' (para 'update')
        // Asumiendo que el parámetro de la ruta es 'institucione' (singular)
        $ignoreId = $this->route('instituciones') ? $this->route('instituciones')->id : null;

        return [
            // 1. Campos de la tabla 'instituciones'
            // Añadimos $ignoreId para que la regla 'unique' funcione en 'update's
            'nombre' => 'required|string|max:255|unique:instituciones,nombre,' . $ignoreId,
            'sigla' => 'required|string|max:50|unique:instituciones,sigla,' . $ignoreId,
            'Encargado' => 'nullable|string|max:255',
            'Estado' => 'required|string|in:Activa,Inactiva,Pendiente', // Usa la columna 'Estado'

            // 2. Campos para la tabla 'institution_contacts'
            'telefono' => 'required|string|max:20',
            'correo_institucional' => 'required|email|max:255',

            // 3. Campos para la tabla pivote 'institucion_pgob'
            // Espera un array de IDs: [1, 2, 3]
            'pgob_ids' => 'nullable|array',
            'pgob_ids.*' => 'integer|exists:pgobs,id', // Valida cada ID en el array

            // 4. Campos para la tabla 'tramites' (servicios)
            // Espera un array de strings: ["Cambio de cédula", "Acta de nacimiento"]
            'servicios' => 'nullable|array',
            'servicios.*' => 'string|max:255', // Valida cada string en el array
        ];
    }

    /**
     * Mensajes de error personalizados en español.
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre de la institución es obligatorio.',
            'nombre.unique' => 'El nombre de esta institución ya existe.',
            'sigla.required' => 'La sigla de la institución es obligatoria.',
            'sigla.unique' => 'La sigla de esta institución ya existe.',
            'Estado.required' => 'El estado es obligatorio.',
            'Estado.in' => 'El estado debe ser "Activa", "Inactiva" o "Pendiente".',

            'telefono.required' => 'El teléfono es obligatorio.',
            'correo_institucional.required' => 'El correo es obligatorio.',
            'correo_institucional.email' => 'El formato del correo no es válido.',

            'pgob_ids.array' => 'Los Puntos GOB asociados deben ser un listado.',
            'pgob_ids.*.exists' => 'Uno de los Puntos GOB seleccionados no es válido.',
            'servicios.array' => 'Los servicios deben ser un listado.',
        ];
    }
}

