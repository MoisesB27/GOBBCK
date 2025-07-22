<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TramiteRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Cambiar si hay políticas de autorización
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'institucion_id' => 'required|exists:instituciones,id',
            'mandatory_fields' => 'nullable|array',
            // Podrías agregar validación para los campos dentro de mandatory_fields si es necesario
        ];
    }
}
