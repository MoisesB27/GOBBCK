<?php

namespace App\Models;

// Imports esenciales
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

// --- Imports de Modelos ---
use App\Models\Instituciones;
use App\Models\Pgob;
use App\Models\Profile;
use App\Models\appointments;
use App\Models\Soporte;

// --- Trait de Roles ---
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    // --- DECLARACIONES DE TRAITS ---
    // HasApiTokens: Para createToken() y tokens()
    // HasRoles: Para hasRole() y assignRole()
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     * Incluye todos los campos de perfil y administración.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        // Campos de Administración/Perfil añadidos
        'apellido',
        'cedula',
        'telefono',
        'institucion_id',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    /**
     * Método de inicialización del modelo.
     */
    protected static function booted(): void
    {
        static::created(function ($user) {
            // Asignar el rol 'usuario' por defecto si el usuario no tiene roles al momento de la creación.
            if (!$user->hasAnyRole(['superadmin', 'admin'])) {
                $user->assignRole('usuario');
            }
        });
    }

    // --- RELACIONES ---

    /**
     * Relación: Usuario pertenece a una Institución (Admin).
     *
     * @return BelongsTo
     */
    public function institucion(): BelongsTo
    {
        return $this->belongsTo(Instituciones::class, 'institucion_id');
    }

    /**
     * Relación: Los Puntos GOB que yo administro (Para el Admin GOB).
     *
     * @return BelongsToMany
     */
            public function adminPgobs(): BelongsToMany
        {
            return $this->belongsToMany(Pgob::class, 'gob_point_admins', 'user_id', 'pgob_id')
                // SOLUCIÓN: Calificamos 'pgobs.id' para resolver la ambigüedad.
                ->select('pgobs.id', 'pgobs.name');
        }

    /**
     * Relación: Un usuario puede tener un perfil (ej. para datos extra del ciudadano).
     */
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Relación: Citas que el usuario ha agendado (para el dashboard de cliente).
     */
    public function citas(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(appointments::class, 'user_id');
    }

    /**
     * Relación: Tickets que el usuario ha creado (para el dashboard de soporte).
     */
    public function soportes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Soporte::class, 'user_id');
    }
}
