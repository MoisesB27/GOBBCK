<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
{
    public function authorize()
    {
        // Cambiar según la lógica de autorización de tu app
        return true;
    }

    public function rules()
    {
        return [
            'name'          => 'required|string|max:255',
            'slug'          => 'required|string|max:255|unique:services,slug,' . $this->route('service'),
            'description'   => 'nullable|string',
            'duration'      => 'required|integer|min:1',
            'logo'          => 'nullable|string|max:255',
            'tramite_id'    => 'required|exists:tramites,id',
            'institucion_id'=> 'required|exists:instituciones,id',
            'ubicacion'     => 'nullable|string|max:255',
            'status_id'        => 'nullable|string|max:50',
            'pgob_id'       => 'nullable|exists:pgobs,id',
        ];
    }
}
