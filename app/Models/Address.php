<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'label',
        'type',
        'recipient_name',
        'street',
        'city',
        'district',
        'postal_code',
        'country',
        'phone',
        'notes',
        'is_default'
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Get the user that owns the address
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get default address
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope to filter by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get the full formatted address
     */
    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->street,
            $this->city,
            $this->district,
            $this->postal_code,
            $this->country
        ]);
        
        return implode(', ', $parts);
    }

    /**
     * Get display name for the address
     */
    public function getDisplayNameAttribute()
    {
        return $this->label ?: $this->street;
    }
}