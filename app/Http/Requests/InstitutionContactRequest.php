<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class InstitutionContactRequest extends FormRequest
{

    /**
     * Obtiene las reglas de validación que se aplican a la petición.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // El campo 'institucion_id' no se valida aquí como 'required' porque se
        // inyecta automáticamente en el controlador (InstitutionContactController.php).

        $rules = [
            'institucion_id' => ['exists:instituciones,id'],
            'tipo' => ['in:correo,telefono,whatsapp,otro'],
            'valor' => ['string', 'max:255', 'required'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_primary' => ['boolean'], // Campo booleano opcional (por defecto debe ser false en la migración)
        ];

        // Para las peticiones PUT/PATCH, hacemos que todos los campos sean opcionales
        // excepto 'institucion_id' (que se ignora de todas formas).
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules = collect($rules)->map(fn($r) => array_merge(['sometimes'], $r))->all();
        }

        return $rules;
    }

    /**
     * Personaliza los mensajes de error.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'institucion_id.exists' => 'La institución especificada no existe.',
            'tipo.in' => 'El tipo de contacto debe ser uno de los siguientes: correo, telefono, whatsapp, otro.',
            'valor.required' => 'El valor del contacto es obligatorio.',
            'valor.max' => 'El valor del contacto no puede exceder los 255 caracteres.',
            'descripcion.max' => 'La descripción no puede exceder los 255 caracteres.',
            'principal.boolean' => 'El campo principal debe ser verdadero o falso.',
        ];
    }
}
