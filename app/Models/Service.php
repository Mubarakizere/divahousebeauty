<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'service_type_id', 'description'];

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function providers()
    {
        return $this->belongsToMany(Provider::class, 'provider_service')
                    ->withPivot('price', 'duration_minutes');
    }
}
