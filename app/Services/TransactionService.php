<?php

namespace App\Services;

use App\Enums\TransactionType;
use App\Models\Transaction;
use App\Models\User;

class TransactionService
{
    public function resolveDeviceId(User $user): string
    {
        return (string) $user->id;
    }

    public function listForUser(User $user, int $limit = 200)
    {
        $deviceId = $this->resolveDeviceId($user);

        $rows = Transaction::where('user_id', $deviceId)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return $rows->map(function (Transaction $t) {
            $type = $t->type instanceof TransactionType
                ? $t->type
                : TransactionType::from((string) $t->type);

            return array_merge($t->toArray(), [
                'type_label' => $type->label(),
                'type_badge' => $type->badgeClass(),
                'type_text_class' => $type->textClass(),
            ]);
        })->values();
    }

    public function cashflowByUser(User $user): array
    {
        $deviceId = $this->resolveDeviceId($user);
        $rows = Transaction::where('user_id', $deviceId)->get(['type', 'amount', 'date']);
        $by = [];
        foreach ($rows as $t) {
            $d = substr((string) $t->date, 0, 10);
            if (! isset($by[$d])) {
                $by[$d] = [
                    TransactionType::IN->value => 0,
                    TransactionType::OUT->value => 0,
                ];
            }
            if ($t->type === TransactionType::IN) {
                $by[$d][TransactionType::IN->value] += (int) $t->amount;
            } else {
                $by[$d][TransactionType::OUT->value] += (int) $t->amount;
            }
        }
        ksort($by);
        $labels = array_keys($by);
        $inData = array_map(fn ($d) => $by[$d][TransactionType::IN->value], $labels);
        $outData = array_map(fn ($d) => $by[$d][TransactionType::OUT->value], $labels);

        return [
            'labels' => $labels,
            'inData' => $inData,
            'outData' => $outData,
        ];
    }
}
