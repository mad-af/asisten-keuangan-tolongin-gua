<?php

namespace App\Models;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'device_id',
        'type',
        'amount',
        'note',
        'date',
    ];

    protected $casts = [
        'type' => TransactionType::class,
    ];
}
