<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'device_id',
        'body',
        'type',
        'attachments',
        'status',
        'metadata',
    ];

    protected $casts = ['attachments' => 'array', 'metadata' => 'array'];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
