<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Instituciones;
use App\Models\Service;
class Tramite extends Model
{

    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'institucion_id',
        'mandatory_fields', // JSON con campos obligatorios
    ];

    protected $casts = [
        'mandatory_fields' => 'array', // Decodifica JSON automático
    ];

    /**
     * Relación: Un trámite pertenece a una institución
     */
    public function institucion()
    {
        return $this->belongsTo(Instituciones::class, 'institucion_id', 'id');
    }

    /**
     * Relación: Un trámite tiene muchos servicios
     */
    public function services()
    {
        return $this->hasMany(Service::class, 'tramite_id', 'id');
    }
}
