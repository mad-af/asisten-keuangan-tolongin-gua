<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'device_name',
        'device_token',
        'device_info',
        'last_seen',
    ];

    protected $hidden = [
        'device_token',
    ];

    protected $casts = [
        'device_info' => 'array',
        'last_seen' => 'datetime',
    ];
}

