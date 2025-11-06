<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class TicketPriorityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Solo los Super Administradores pueden gestionar estos datos.
     */
    public function authorize(): bool
    {
        // Asumo que el rol para el administrador principal es 'superadmin'
        return Auth::check() && Auth::user()->hasRole('superadmin');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $priorityId = $this->route('priority'); // Obtengo el ID si es un UPDATE (para ignorarlo en la unicidad)

        return [
            // El nombre debe ser único, requerido, y de tipo string
            'name' => [
                'required',
                'string',
                'max:255',
                // Ignora la fila actual al chequear la unicidad
                Rule::unique('ticket_priorities', 'name')->ignore($priorityId),
            ],

            // Descripción es opcional
            'description' => 'nullable|string',

            // Código de color (HEX)
            'color_code' => 'required|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', // Código HEX (ej: #FF0000 o #F00)

            // is_active (booleano, a veces se envía como string 'true'/'false')
            'is_active' => 'sometimes|boolean',
        ];
    }

    /**
     * Prepara los datos para la validación.
     * Convierte el campo 'is_active' a booleano si viene como string.
     */
    protected function prepareForValidation()
    {
        if ($this->has('is_active') && is_string($this->is_active)) {
            $this->merge([
                'is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN),
            ]);
        }
    }

    /**
     * Mensajes de error personalizados.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la prioridad es obligatorio.',
            'name.unique' => 'Ya existe una prioridad con este nombre.',
            'color_code.required' => 'Se requiere un código de color HEX.',
            'color_code.regex' => 'El código de color debe ser un valor HEX válido (ej: #00FF00).',
        ];
    }
}
