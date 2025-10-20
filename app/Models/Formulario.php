<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Pgob;
use App\Models\Service;
use App\Models\Appointments;
use App\Models\FormularioStatus;

class Formulario extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'cedula',
        'direccion',
        'telefono',
        'discapacidad',
        'user_id',
        'pgob_id',
        'tipo_tramite',
        'tipo_beneficiario',
        'service_id',
        'appointment_id',
        'status_id',
        'submitted_at',
    ];

    protected $casts = [
        'data' => 'array',
        'submitted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function pgob()
    {
        return $this->belongsTo(Pgob::class, 'pgob_id', 'id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }

    public function appointment()
    {
        // Aquí corregimos el modelo a Appointment (singular)
        return $this->belongsTo(Appointments::class, 'appointment_id', 'id');
    }

    public function status()
    {
        return $this->belongsTo(FormularioStatus::class, 'status_id');
    }
}
