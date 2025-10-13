<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TicketStatusRequest extends FormRequest
{
    /**
     * Determino si el usuario está autorizado para hacer esta petición.
     */
    public function authorize(): bool
    {
        // Asumo que la autorización se manejará a nivel de Controller/Middleware (role:super-admin)
        return true;
    }

    /**
     * Obtengo las reglas de validación que se aplican a la petición.
     */
    public function rules(): array
    {
        $statusId = $this->route('status'); // Obtengo el ID si es un UPDATE

        return [
            // El nombre debe ser único
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('ticket_statuses', 'name')->ignore($statusId),
            ],
            // Campo Enum: solo permito los valores definidos en la migración
            'priority_level' => [
                'required',
                'string',
                Rule::in(['urgente', 'prioritaria', 'ordinaria']),
            ],
            // Código de color opcional
            'color_code' => 'nullable|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            // Si el estado está activo
            'is_active' => 'sometimes|boolean',
        ];
    }

    /**
     * Preparamo los datos para la validación
     */
    protected function prepareForValidation()
    {
        if ($this->has('is_active') && is_string($this->is_active)) {
            $this->merge([
                'is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN),
            ]);
        }
    }
}
