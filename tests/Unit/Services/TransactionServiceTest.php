<?php

namespace Tests\Unit\Services;

use App\Enums\TransactionType;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_for_user_returns_mapped_fields(): void
    {
        $user = User::create(['name' => 'n']);
        Transaction::createIn($user->id, 50, 'ni', '2025-01-01');
        $svc = new TransactionService();
        $rows = $svc->listForUser($user);
        $this->assertCount(1, $rows);
        $this->assertArrayHasKey('type_label', $rows[0]);
        $this->assertArrayHasKey('type_badge', $rows[0]);
        $this->assertArrayHasKey('type_text_class', $rows[0]);
    }

    public function test_cashflow_by_user_aggregates_per_day(): void
    {
        $user = User::create(['name' => 'n']);
        Transaction::createIn($user->id, 10, 'a', '2025-01-01');
        Transaction::createOut($user->id, 5, 'b', '2025-01-01');
        $svc = new TransactionService();
        $cf = $svc->cashflowByUser($user);
        $this->assertSame(['2025-01-01'], $cf['labels']);
        $this->assertSame([10], $cf['inData']);
        $this->assertSame([5], $cf['outData']);
    }

    public function test_monthly_stats_by_user_returns_totals(): void
    {
        $user = User::create(['name' => 'n']);
        Transaction::createIn($user->id, 10, 'a', '2025-01-15');
        Transaction::createOut($user->id, 6, 'b', '2025-01-16');
        $svc = new TransactionService();
        $stats = $svc->monthlyStatsByUser($user, '2025-01');
        $this->assertSame('2025-01', $stats['month']);
        $this->assertSame(10, $stats['in_total']);
        $this->assertSame(6, $stats['out_total']);
        $this->assertSame(4, $stats['net_total']);
    }
}

