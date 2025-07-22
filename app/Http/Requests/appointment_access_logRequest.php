<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class Storeappointment_access_logRequest extends FormRequest
{
    public function authorize()
    {
        // Cambia a true o añade lógica de autorización según tu auth
        return true;
    }

    public function rules()
    {
        return [
            'appointment_id' => 'required|exists:appointments,id',
            'accessed_at'    => 'nullable|date',
            'ip_address'     => 'nullable|ip',
        ];
    }
}
