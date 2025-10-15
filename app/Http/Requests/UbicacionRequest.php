<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UbicacionRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Ajusta autorización según tu lógica
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'tipo' => 'required|string|max:100',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'contacto' => 'nullable|string|max:255',
            'radio_cobertura' => 'nullable|integer|min:0',
            'extras' => 'nullable|array',
            'extras.*' => 'sometimes|string',  // Si es array de strings
            'pgob_id' => 'required|uuid|exists:pgobs,id',
        ];
    }
}
