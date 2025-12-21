<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalonService extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'duration'];

    public function providers()
{
    return $this->belongsToMany(ServiceProvider::class, 'service_provider_services');
}
}
