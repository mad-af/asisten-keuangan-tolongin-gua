<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrchestratorMemory extends Model
{
    protected $fillable = [
        'user_id',
        'content',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];
}
