<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\User;
use App\Models\GobPointAdmin;
use App\Models\appointments;
use App\Models\Soporte;
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
        'is_active',
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
        'is_active' => 'boolean',
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


    public function instituciones(): BelongsToMany
    {
        // Usa la tabla pivote 'institucion_pgob' para vincular con el modelo Institucion.
        return $this->belongsToMany(Instituciones::class, 'institucion_pgob', 'pgob_id', 'institucion_id');
    }


    /**
     * Ubicaciones asociadas a este pgob.
     */
    public function ubicacions()
    {
        return $this->hasMany(Ubicacion::class, 'pgob_id', 'id');
    }

    public function admins()
    {
        return $this->belongsToMany(User::class, 'gob_point_admins', 'pgob_id', 'user_id')
                    ->using(GobPointAdmin::class);
    }

    /**
     * Citas agendadas en este Punto GOB.
     * Necesario para los gráficos y el filtrado del Dashboard
     */
    public function appointments()
    {
        return $this->hasMany(appointments::class, 'pgob_id');
    }

    /**
     * Soportes asociados a este Punto GOB.
     */
    public function soportes()
    {
        return $this->hasMany(Soporte::class, 'pgob_id');
    }
}

