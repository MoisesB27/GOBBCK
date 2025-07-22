<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AppointmentServiceRequest extends FormRequest
{
    public function authorize()
    {
        // Puedes ajustar según permisos de usuario
        return true;
    }

    public function rules()
    {
        return [
            'appointment_id' => 'required|exists:appointments,id',
            'service_id' => 'required|exists:services,id',
            'quantity' => 'required|integer|min:1',
            'special_requests' => 'nullable|string|max:255',
        ];
    }
}
