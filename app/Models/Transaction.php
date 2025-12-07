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

    public static function editByAmount(string|int $userId, int $amount, array $changes): ?self
    {
        $tx = self::where('user_id', $userId)->where('amount', $amount)->orderByDesc('date')->first();
        if (! $tx) {
            return null;
        }
        $allowed = ['amount', 'note', 'type', 'date'];
        foreach ($allowed as $key) {
            if (array_key_exists($key, $changes) && $changes[$key] !== null) {
                if ($key === 'type') {
                    $val = $changes[$key];
                    $enum = $val instanceof TransactionType ? $val : TransactionType::tryFrom((string) $val);
                    if ($enum) {
                        $tx->type = $enum;
                    }
                } else {
                    $tx->{$key} = $changes[$key];
                }
            }
        }
        $tx->save();

        return $tx;
    }

    public static function editByNote(string|int $userId, string $note, array $changes): ?self
    {
        $tx = self::where('user_id', $userId)->where('note', $note)->orderByDesc('date')->first();
        if (! $tx) {
            return null;
        }
        $allowed = ['amount', 'note', 'type', 'date'];
        foreach ($allowed as $key) {
            if (array_key_exists($key, $changes) && $changes[$key] !== null) {
                if ($key === 'type') {
                    $val = $changes[$key];
                    $enum = $val instanceof TransactionType ? $val : TransactionType::tryFrom((string) $val);
                    if ($enum) {
                        $tx->type = $enum;
                    }
                } else {
                    $tx->{$key} = $changes[$key];
                }
            }
        }
        $tx->save();

        return $tx;
    }

    public static function editByType(string|int $userId, TransactionType|string $type, array $changes): ?self
    {
        $enum = $type instanceof TransactionType ? $type : TransactionType::tryFrom((string) $type);
        if (! $enum) {
            return null;
        }
        $tx = self::where('user_id', $userId)->where('type', $enum)->orderByDesc('date')->first();
        if (! $tx) {
            return null;
        }
        $allowed = ['amount', 'note', 'type', 'date'];
        foreach ($allowed as $key) {
            if (array_key_exists($key, $changes) && $changes[$key] !== null) {
                if ($key === 'type') {
                    $val = $changes[$key];
                    $enum2 = $val instanceof TransactionType ? $val : TransactionType::tryFrom((string) $val);
                    if ($enum2) {
                        $tx->type = $enum2;
                    }
                } else {
                    $tx->{$key} = $changes[$key];
                }
            }
        }
        $tx->save();

        return $tx;
    }
}
