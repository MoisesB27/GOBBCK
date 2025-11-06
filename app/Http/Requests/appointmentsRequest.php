<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule; // Útil para reglas complejas

class appointmentsRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar esta solicitud.
     */
    public function authorize(): bool
    {
        // Solo usuarios autenticados pueden crear citas
        return Auth::check();
    }

    /**
     * Prepara los datos para la validación.
     * Añadimos 'user_id' automáticamente desde el usuario autenticado.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            // Asegura que user_id siempre esté presente antes de validar
            'user_id' => Auth::id(),
            // (Opcional) Sanitizar/limpiar otros campos si es necesario
        ]);
    }


    /**
     * Obtiene las reglas de validación que se aplican a la solicitud.
     * Fusiona campos de 'appointments' y 'formularios'.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            // Campo oculto añadido automáticamente por prepareForValidation
            'user_id'           => 'required|exists:users,id',

            // --- Campos de la Cita ---
            'service_id'        => 'required|exists:services,id',
            'start_time'        => 'required|date_format:Y-m-d H:i:s|after_or_equal:now',
            'end_time'          => 'required|date_format:Y-m-d H:i:s|after:start_time',

            // --- Campos del Formulario (Fusionados) ---
            'nombre'            => 'required|string|max:255',
            'apellido'          => 'required|string|max:255',
            'email'             => 'required|email|max:255',
            'telefono'          => 'required|string|max:20',
            'cedula'            => 'required|string|max:13',
            'direccion'         => 'nullable|string|max:500',
            'tiene_discapacidad' => 'required|boolean', // Corregido de 'discapacidad'

            // --- Campo Clave para Lógica Condicional (Corregido para ENUM) ---
            'tipo_beneficiario' => ['required', 'string', Rule::in(['para_mi', 'otra_persona', 'menor'])],

            // --- Datos del Menor (Corregido para ENUM) ---
            'datos_menor'                 => 'nullable|json',
            'datos_menor.nombre_menor'    => 'required_if:tipo_beneficiario,menor|nullable|string|max:255',
            'datos_menor.apellido_menor'  => 'required_if:tipo_beneficiario,menor|nullable|string|max:255',
        ];
    }

    /**
     * (Opcional) Mensajes de error personalizados en español.
     */
    public function messages(): array
    {
        return [
            'service_id.required' => 'Debe seleccionar un servicio.',
            'service_id.exists'   => 'El servicio seleccionado no es válido.',
            'start_time.required' => 'Debe seleccionar una hora de inicio.',
            'start_time.date_format' => 'El formato de la hora de inicio no es válido (use AAAA-MM-DD HH:MM:SS).',
            'start_time.after_or_equal' => 'La hora de inicio debe ser ahora o en el futuro.',
            'end_time.required' => 'La hora de fin es requerida.',
            'end_time.after' => 'La hora de fin debe ser posterior a la hora de inicio.',

            'nombre.required'   => 'El campo nombre del solicitante es obligatorio.',
            'apellido.required' => 'El campo apellido del solicitante es obligatorio.',
            'email.required'    => 'El campo correo electrónico es obligatorio.',
            'email.email'       => 'El correo electrónico debe tener un formato válido.',
            'telefono.required' => 'El campo teléfono es obligatorio.',
            'cedula.required'   => 'El campo cédula del solicitante es obligatorio.',
            'tiene_discapacidad.required' => 'Debe indicar si tiene alguna discapacidad.',
            'tipo_beneficiario.required' => 'Debe seleccionar para quién es la cita.',
            'tipo_beneficiario.in' => 'El tipo de beneficiario seleccionado no es válido (debe ser: para_mi, otra_persona, o menor).',

            // Mensajes para los campos condicionales del menor
            'datos_menor.nombre_menor.required_if' => 'El nombre del menor es obligatorio cuando la cita es para un menor.',
            'datos_menor.apellido_menor.required_if' => 'El apellido del menor es obligatorio cuando la cita es para un menor.',
        ];
    }
}

