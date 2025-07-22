<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Tramite;
use App\Models\Service;


class Instituciones extends Model
{
    use HasFactory;

    protected $fillable = [
        
        'nombre',
        'sigla'
        // agregar otros campos que tengas en la tabla
    ];

    /**
     * Relación: una institución tiene muchos trámites.
     */
    public function tramites()
    {
        return $this->hasMany(Tramite::class, 'institucion_id', 'id');
    }

    /**
     * Relación: una institución tiene muchos servicios.
     */
    public function services()
    {
        return $this->hasMany(Service::class, 'institucion_id', 'id');
    }
}
