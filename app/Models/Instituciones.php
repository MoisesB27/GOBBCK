<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Pgob;
use App\Models\Tramite;
use App\Models\Service;
use App\Models\InstitutionContact; // Importo el nuevo modelo de contactos

class Instituciones extends Model
{
    use HasFactory;

    protected $fillable = [

        'nombre',
        'sigla',
    ];

    /**
     * Relación: Tengo muchos trámites.
     */
    public function tramites()
    {
        return $this->hasMany(Tramite::class, 'institucion_id', 'id');
    }

    public function pgobs(): BelongsToMany
    {
        // Usa la tabla pivote 'institucion_pgob' para vincular con el modelo Pgob.
        // Los argumentos son explícitos para asegurar que usemos tus nombres de columna.
        return $this->belongsToMany(Pgob::class, 'institucion_pgob', 'institucion_id', 'pgob_id');
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
