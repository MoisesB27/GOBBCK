<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FormularioRequest extends FormRequest
{
    public function authorize()
    {
        
        return true;
    }

    public function rules()
    {
        return [
            'user_id'        => 'nullable|exists:users,id',
            'pgob_id'        => 'required|exists:pgobs,id',
            'service_id'     => 'required|exists:services,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'data'           => 'required|array',
            'status'         => 'nullable|string|max:50',
            'submitted_at'   => 'nullable|date',
        ];
    }
}
