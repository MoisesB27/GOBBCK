<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use App\Models\User;

class Profile extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        // otros campos que tengas
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Cambiado de función a propiedad para $casts
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->getKey()) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /**
     * Relación: Un usuario tiene un perfil.
     *
     * @return HasOne
     */
    public function User(): HasOne
    {
        return $this->hasOne(User::class, 'user_id', 'id');
    }

    // Otras relaciones y métodos según tu proyecto...
}
