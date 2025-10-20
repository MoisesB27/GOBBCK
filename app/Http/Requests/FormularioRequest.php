<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FormularioRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Permitir acceso, ajusta si usas políticas
    }

    public function rules()
    {
        return [
            'nombre'          => 'required|string|max:255',
            'apellido'        => 'required|string|max:255',
            'email'           => 'required|email|max:255',
            'cedula'          => 'nullable|string|max:20',
            'direccion'       => 'nullable|string|max:255',
            'telefono'        => 'nullable|string|max:20',
            'discapacidad'    => 'nullable|boolean',

            'user_id'         => 'nullable|exists:users,id',
            'pgob_id'         => 'nullable|exists:pgobs,id',
            'institucion_id'  => 'required|exists:instituciones,id',
            'service_id'      => 'nullable|exists:services,id',
            'appointment_id'  => 'nullable|exists:appointments,id',

            'tipo_tramite'    => 'nullable|string|max:255',
            'tipo_beneficiario' => 'required|in:para_mi,otra_persona,menor',

            'fecha_cita'      => 'nullable|date',
            'hora_cita'       => 'nullable|date_format:H:i:s',

            'status_id'       => 'nullable|exists:formulario_statuses,id',
            'submitted_at'    => 'nullable|date',
        ];
    }
}

