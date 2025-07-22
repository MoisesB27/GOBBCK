<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PgobRequest extends FormRequest
{
    public function authorize()
    {
        // Cambia según lógica de autorización real
        return true;
    }

    public function rules()
    {
        return [
            'name'                      => 'required|string|max:255',
            'descripcion'               => 'nullable|string',
            'business_hours'            => 'nullable|array',
            'business_hours.*'          => 'string', // O ajusta tipo según estructura esperada
            'appointment_limit'         => 'nullable|integer|min:0',
            'appointment_limit_per_user'=> 'nullable|integer|min:0',
        ];
    }
}