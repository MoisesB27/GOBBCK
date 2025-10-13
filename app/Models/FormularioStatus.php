<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class FormularioStatus extends Model
{
    use HasFactory;

    protected $table = 'formulario_statuses'; // Nombre de tabla inferido

    /**
     * Los campos que se pueden asignar masivamente.
     * Asumimos una estructura similar a las otras tablas de estado.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'color_code',
        'order', // Para definir el orden de visualización
        'is_active',
        'description',
    ];

    /**
     * Relación One-to-Many: Un estado puede tener muchos formularios.
     */
    public function formularios()
    {
        return $this->hasMany(Formulario::class, 'status_id');
    }
}
