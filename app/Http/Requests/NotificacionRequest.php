<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NotificacionRequest extends FormRequest
{
    public function authorize()
    {
        // Ajusta según reglas de autorización
        return true;
    }

    public function rules()
    {
        return [
            'user_id'    => 'nullable|exists:users,id',
            'type'       => 'required|string|max:255',
            'message'    => 'required|string',
            'fecha'      => 'nullable|date',
            'publico'    => 'boolean',
            'tipo'       => 'nullable|string|max:255',
            'is_read'    => 'boolean',
            'metadata'   => 'nullable|array',
            'service_id' => 'nullable|exists:services,id',
            'pgob_id'    => 'nullable|exists:pgobs,id',
        ];
    }
}
