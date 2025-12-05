<?php

namespace App\Models;

use App\Enums\MessageType;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'user_id',
        'body',
        'type',
    ];

    protected $casts = [
        'type' => MessageType::class,
    ];
}
