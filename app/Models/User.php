<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

// --- NUEVOS IMPORTS ---
use Spatie\Permission\Traits\HasRoles; // 1. Para que funcione hasRole()
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // 2. Para la relación adminPgobs()
use Illuminate\Database\Eloquent\Relations\HasOne; // 3. Para type-hinting en profile()

// Corrijo el import de tu modelo Profile
use App\Models\Profile;
use App\Models\Pgob; // Importo Pgob para la relación
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    // --- USO EL TRAIT HasRoles ---
    use  HasRoles, HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'cedula',
        'email',
        'password',
    ];


    protected static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            // Asignar el rol 'usuario' por defecto si el usuario no tiene roles al momento de la creación.
            // Esto es CRÍTICO para resolver el error 403 en nuevos registros.
            if (!$user->hasAnyRole(['superadmin', 'admin'])) {
                $user->assignRole('usuario');
            }
        });
    }


    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Relación: Los Puntos GOB que yo administro (Many-to-Many).
     * ¡ESTA ES LA CLAVE PARA EL FILTRO adminPgobs()!
     */
    public function adminPgobs(): BelongsToMany
    {
        // Uso la tabla pivote 'gob_point_admins' que yo creé
        return $this->belongsToMany(Pgob::class, 'gob_point_admins', 'user_id', 'pgob_id');
    }
}
