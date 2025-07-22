<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Service;
use App\Models\Ubicacion;

class Pgob extends Model
{
    use HasFactory;


    /**
     * Los campos asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'descripcion',
        'business_hours',
        'appointment_limit',
        'appointment_limit_per_user',
    ];

    /**
     * Casts de atributos especiales.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'business_hours' => 'array',
        'appointment_limit' => 'integer',
        'appointment_limit_per_user' => 'integer',
    ];

    /**
     * Define relaciones si las tienes, por ejemplo:
     *
     * Servicios disponibles en este punto gob
     */
    public function services()
    {
        return $this->hasMany(Service::class, 'pgob_id', 'id');
    }

    /**
     * Ubicaciones asociadas a este pgob.
     */
    public function ubicacions()
    {
        return $this->hasMany(Ubicacion::class, 'pgob_id', 'id');
    }
}
