<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentoRequest extends FormRequest
{
    public function authorize()
    {
        // Ajusta según necesidades de autenticación o permisos
        return true;
    }

    public function rules()
    {
        return [
            'user_id'       => 'required|exists:users,id',
            'document_type' => 'required|string|max:255',
            'file_url'      => 'required|url|max:1000',
            'status'        => 'nullable|string|max:50',
            'verified_at'   => 'nullable|date',
        ];
    }
}
