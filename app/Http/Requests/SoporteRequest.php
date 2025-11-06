<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SoporteRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta petición.
     */
    public function authorize(): bool
    {
        // Cualquier usuario autenticado puede crear un ticket.
        // La gestión del ticket (update) está protegida por middleware en el controlador.
        return Auth::check();
    }

    /**
     * Prepara los datos para la validación.
     * Inyecta el user_id y el estado/prioridad por defecto si no vienen.
     */
    protected function prepareForValidation(): void
    {
        // 1. Asigna el user_id del autenticado (si no viene en el request)
        if (Auth::check()) {
            $this->merge(['user_id' => Auth::id()]);
        }

        // 2. Asigna ID por defecto si es una creación de ticket simple (ID 1 = Abierto)
        if (!$this->has('status_id')) {
            $this->merge(['status_id' => 1]);
        }

        // 3. Asigna ID por defecto si es una creación de ticket simple (ID 1 = Ordinaria)
        if (!$this->has('priority_id')) {
            $this->merge(['priority_id' => 1]);
        }
    }

    /**
     * Obtiene las reglas de validación que se aplican a la solicitud.
     */
    public function rules(): array
    {
        // Lógica: Si el usuario NO está logueado, exigimos nombre y correo para contacto.
        // Usamos una verificación de Auth::check() que se ejecuta antes de la validación.
        $isLogged = Auth::check();

        return [
            // --- CAMPOS DEL REPORTE ---
            'asunto'            => 'required|string|max:255',
            'descripcion'       => 'required|string',

            // Campos Condicionales (Solo necesarios si el user_id es null, es decir, es un reporte anónimo)
            'nombre_completo'   => [Rule::requiredIf(!$isLogged), 'nullable', 'string', 'max:255'],
            'correo_electronico'=> [Rule::requiredIf(!$isLogged), 'nullable', 'email', 'max:255'],

            // --- CAMPOS DE GESTIÓN (FKs) ---
            'user_id'           => 'nullable|exists:users,id', // Se inyecta por prepareForValidation
            'pgob_id'           => 'nullable|exists:pgobs,id', // Contexto (Punto GOB afectado)

            // Estado y Prioridad (Vienen en el request si el admin los envía, o usamos el valor por defecto)
            'status_id'         => 'required|exists:ticket_statuses,id',
            'priority_id'       => 'required|exists:ticket_priorities,id',

            // Usuario Asignado (Solo lo usa el backoffice)
            'assigned_to_user_id' => 'nullable|exists:users,id',
        ];
    }

    /**
     * Mensajes de error en español.
     */
    public function messages(): array
    {
        return [
            'asunto.required' => 'El campo asunto es obligatorio.',
            'descripcion.required' => 'El campo descripción es obligatorio.',
            'nombre_completo.required_if' => 'El nombre es obligatorio para usuarios no registrados.',
            'correo_electronico.required_if' => 'El correo es obligatorio para usuarios no registrados.',
            'correo_electronico.email' => 'El formato del correo electrónico no es válido.',
            'status_id.exists' => 'El estado del ticket no es válido.',
            'priority_id.exists' => 'La prioridad del ticket no es válida.',
        ];
    }
}
