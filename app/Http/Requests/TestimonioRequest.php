<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TestimonioRequest extends FormRequest
{
    public function authorize()
    {
        // Ajusta según tu lógica de autorización
        return true;
    }

    public function rules()
    {
        return [
            'user_id'   => 'nullable|exists:users,id',
            'name'      => 'required|string|max:255',
            'username'  => 'required|string|max:100',
            'message'   => 'required|string',
            'rating'    => 'required|integer|min:1|max:5',
            'photo_url' => 'nullable|url|max:255',
        ];
    }
}
