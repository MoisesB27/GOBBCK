<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Permitir siempre (o agrega lógica si quieres)
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|string|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ];
    }
}
