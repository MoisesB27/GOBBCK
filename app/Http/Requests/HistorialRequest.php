<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HistorialRequest extends FormRequest
{
    public function authorize()
    {
        // Cambia esta lógica según tu sistema de autorización
        return true;
    }

    public function rules()
    {
        return [
            'appointment_id'    => 'required|exists:appointments,id',
            'tipo_servicio_id'  => 'required|exists:services,id',
            'entidad_id'        => 'required|exists:instituciones,id',
            'fecha'             => 'required|date',
            'hora'              => 'required|date_format:H:i:s',
            'estado'            => 'required|string|max:255',
            'ticket'            => 'nullable|string|max:255',
            'detalles_ticket'   => 'nullable|string',
            'user_id'           => 'required|exists:users,id',
        ];
    }
}
