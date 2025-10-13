<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Service;

class ServiceStatus extends Model
{
    use HasFactory;

    protected $table = 'service_statuses';

    protected $fillable = ['name', 'color_code', 'description', 'is_visible'];

    // Relación One-to-Many: Un estado puede tener muchos servicios.
    public function services()
    {
        return $this->hasMany(Service::class, 'status_id');
    }
}
