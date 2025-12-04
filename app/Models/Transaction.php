<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'amount',
        'type',
        'description',
        'date',
        'metadata',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
