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

    public static function lastTenRoleContentByUser(string|int $userId): array
    {
        $rows = self::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->reverse()
            ->values();

        return $rows->map(function (self $m) {
            $role = $m->type instanceof MessageType ? $m->type->value : (string) $m->type;

            return [
                'role' => $role,
                'content' => (string) $m->body,
            ];
        })->all();
    }
}
