<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Instituciones;

class InstitutionContact extends Model
{
    use HasFactory;

    protected $table = 'institution_contacts';

    protected $fillable = [
        'institucion_id',
        'tipo', // 'correo', 'telefono', 'whatsapp', 'otro'
        'valor', // el número o la dirección
        'descripcion',
        'principal',
    ];

    /**
     * Relación Many-to-One: Un contacto pertenece a una sola institución.
     */
    public function institucion()
    {
        // Asume que tu modelo para Instituciones se llama Institucion.
        return $this->belongsTo(Instituciones::class, 'institucion_id');
    }
}
