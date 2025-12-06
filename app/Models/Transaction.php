<?php

namespace App\Models;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'note',
        'date',
    ];

    protected $casts = [
        'type' => TransactionType::class,
    ];

    public static function createIn(string|int $userId, int $amount, ?string $note = null, ?string $date = null): self
    {
        return self::create([
            'user_id' => $userId,
            'type' => TransactionType::IN,
            'amount' => $amount,
            'note' => $note,
            'date' => $date ?: now()->format('Y-m-d'),
        ]);
    }

    public static function createOut(string|int $userId, int $amount, ?string $note = null, ?string $date = null): self
    {
        return self::create([
            'user_id' => $userId,
            'type' => TransactionType::OUT,
            'amount' => $amount,
            'note' => $note,
            'date' => $date ?: now()->format('Y-m-d'),
        ]);
    }

    public function scopeIn($query)
    {
        return $query->where('type', TransactionType::IN);
    }

    public function scopeOut($query)
    {
        return $query->where('type', TransactionType::OUT);
    }
}
