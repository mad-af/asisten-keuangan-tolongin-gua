<?php

namespace App\Enums;

enum MessageType: string
{
    case user = 'user';
    case assistant = 'assistant';

    public function label(): string
    {
        return match ($this) {
            self::user => 'User',
            self::assistant => 'Assistant',
        };
    }
}

