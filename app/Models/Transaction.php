<?php

namespace App\Models;

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
}
