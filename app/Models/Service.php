<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Institucion;
use App\Models\Pgob;
use App\Models\Tramite;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{

    use HasFactory;
   
    protected $fillable = [
        'name',
        'slug',
        'description',
        'duration',
        'logo',
        'tramite_id',
        'institucion_id',
        'ubicacion',
        'status',
        'pgob_id',
    ];

    /**
     * Casts para atributos con tipos específicos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'duration' => 'integer',
    ];

    /**
     * Relación con tramite.
     */
    public function tramite()
    {
        return $this->belongsTo(Tramite::class, 'tramite_id', 'id');
    }

    /**
     * Relación con institución.
     */
    public function institucion()
    {
        return $this->belongsTo(Instituciones::class, 'institucion_id', 'id');
    }

    /**
     * Relación con punto gob.
     */
    public function pgob()
    {
        return $this->belongsTo(Pgob::class, 'pgob_id', 'id');
    }
}
