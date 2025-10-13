<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Pgob;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ubicacion extends Model
{
    use HasFactory;

    /**
     * Los campos que pueden asignarse masivamente.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'name',
        'tipo',
        'latitude',
        'longitude',
        'address',
        'city',
        'state',
        'zip_code',
        'contacto',
        'radio_cobertura',
        'extras',
        'pgob_id',
    ];

    /**
     * Casts para atributos específicos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'radio_cobertura' => 'integer',
        'extras' => 'array',
    ];

    /**
     * Relación: Ubicación pertenece a un Punto Gob.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pgob()
    {
        return $this->belongsTo(Pgob::class, 'pgob_id', 'id');
    }
}
