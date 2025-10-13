<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\appointments;
use Illuminate\Database\Eloquent\Model;

class AppointmentStatus extends Model
{
    use HasFactory;

    protected $table = 'appointment_statuses';

    protected $fillable = ['name', 'color_code', 'order', 'is_active'];

    // Relación One-to-Many: Un estado puede tener muchas citas.
    public function appointments()
    {
        return $this->hasMany(appointments::class, 'status_id');
    }
}
