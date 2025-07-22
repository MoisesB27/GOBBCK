<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActividadeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        // Cambia a true si no usas middleware o alguna autorización específica
        return true;
    }

    public function rules()
    {
        return [
            'user_id'       => 'required|exists:users,id',
            'activity_type' => 'required|string|max:255',
            'description'   => 'nullable|string|max:1000',
            'ip_address'    => 'nullable|ip',
            'device_info'   => 'nullable|string',
        ];
    }
}
