<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth; // Para la autorización

class TicketStatusRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta petición.
     */
    public function authorize(): bool
    {
        // Asumimos que solo un 'superadmin' puede gestionar los estados de los tickets
        return Auth::check() && Auth::user()->hasRole('superadmin');
    }

    /**
     * Obtiene las reglas de validación que se aplican a la petición.
     * (CORREGIDO: Se eliminó 'priority_level' de aquí)
     */
    public function rules(): array
    {
        $statusId = $this->route('status'); // Obtiene el ID del estado si es un UPDATE

        return [
            // El nombre debe ser único, ignorando el ID actual al actualizar
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('ticket_statuses', 'name')->ignore($statusId),
            ],

            // Campos de la migración 'create_ticket_statuses_table.php'
            'color_code' => [
                'required',
                'string',
                'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/' // Valida código HEX
            ],
            'is_active' => 'sometimes|boolean',
            'description' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Prepara los datos para la validación (maneja checkboxes)
     */
    protected function prepareForValidation()
    {
        // Convierte "true", "on", "1" a un booleano verdadero
        if ($this->has('is_active')) {
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
            'name.required' => 'El nombre del estado es obligatorio.',
            'name.unique' => 'Este nombre de estado ya existe.',
            'color_code.required' => 'El código de color es obligatorio.',
            'color_code.regex' => 'El código de color debe ser un formato HEX válido (ej. #FFFFFF).',
        ];
    }
}

