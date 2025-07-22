<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SoporteRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Ajusta la autorización según tu lógica
    }

    public function rules()
    {
        return [
            'user_id'           => 'nullable|exists:users,id',
            'nombre_completo'   => 'required|string|max:255',
            'correo_electronico'=> 'required|email|max:255',
            'asunto'            => 'required|string|max:255',
            'descripcion'       => 'required|string',
        ];
    }
}
