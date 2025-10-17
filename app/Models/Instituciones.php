<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Tramite;
use App\Models\Service;
use App\Models\InstitutionContact; // Importo el nuevo modelo de contactos

class Instituciones extends Model
{
    use HasFactory;

    protected $fillable = [

        'nombre',
        'sigla'
    ];

    /**
     * Relación: Tengo muchos trámites.
     */
    public function tramites()
    {
        return $this->hasMany(Tramite::class, 'institucion_id', 'id');
    }

    /**
     * Relación: Tengo muchos servicios.
     */
    public function services()
    {
        return $this->hasMany(Service::class, 'institucion_id', 'id');
    }
    /**
     * Relación: Tengo muchos contactos.
     */
    public function contacts()
    {
        // Se relaciona con la tabla institution_contacts
        return $this->hasMany(InstitutionContact::class, 'institucion_id');
    }
}
