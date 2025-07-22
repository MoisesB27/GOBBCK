<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class appointmentsRequest extends FormRequest
{
    public function authorize()
    {
        // Puedes agregar lógica de autorización según necesidad
        return true;
    }

    public function rules()
    {
        return [
            'user_id'          => 'required|exists:users,id',
            'service_id'       => 'required|exists:appointment_services,id',
            'pgob_id'          => 'required|exists:pgobs,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'status'           => 'required|string|max:50',
            'assigned_to'      => 'nullable|exists:users,id',
            'qr_code'          => 'nullable|string|max:255',
            'comments'         => 'nullable|string|max:1000',
        ];
    }
}
