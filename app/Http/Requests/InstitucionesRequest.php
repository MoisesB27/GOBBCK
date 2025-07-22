<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InstitucionesRequest extends FormRequest
{
    public function authorize()
    {
        
        return true;
    }

    public function rules()
    {
        return [
            'nombre' => 'required|string|max:255',
            'sigla' => 'required|string|max:50',
        ];
    }
}
