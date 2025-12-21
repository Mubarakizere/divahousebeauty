<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $table = 'contact_messages';

    protected $fillable = [
        'name', 'email', 'subject', 'message', 'replied',
    ];

    protected $casts = [
        'replied' => 'boolean',
    ];
}
