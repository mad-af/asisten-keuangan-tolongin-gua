<?php

namespace App\Enums;

enum TransactionType: string
{
    case IN = 'IN';
    case OUT = 'OUT';

    public function label(): string
    {
        return match ($this) {
            self::IN => 'Pemasukan',
            self::OUT => 'Pengeluaran',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::IN => 'badge-success',
            self::OUT => 'badge-error',
        };
    }

    public function textClass(): string
    {
        return match ($this) {
            self::IN => 'text-success',
            self::OUT => 'text-error',
        };
    }
}
