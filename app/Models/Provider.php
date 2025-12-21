<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'phone_number', 'location'];

    public function services()
    {
        return $this->belongsToMany(Service::class, 'provider_service')
                    ->withPivot('price', 'duration_minutes');
    }
}
