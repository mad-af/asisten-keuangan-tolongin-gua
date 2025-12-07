<?php

namespace Tests\Unit\Services;

use App\Services\FinanceAnalyzeResult;
use App\Services\FinanceAnalyzeService;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FinanceAnalyzeServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_inject_user_id_filter_adds_where_when_missing(): void
    {
        $sql = 'SELECT * FROM transactions';
        $filtered = FinanceAnalyzeService::injectUserIdFilter($sql, 'u1');
        $this->assertStringContainsString("WHERE user_id = 'u1'", $filtered);
    }

    public function test_inject_user_id_filter_inserts_into_existing_where(): void
    {
        $sql = 'SELECT * FROM transactions WHERE amount > 0';
        $filtered = FinanceAnalyzeService::injectUserIdFilter($sql, 'u1');
        $this->assertStringContainsString("WHERE user_id = 'u1' AND", $filtered);
    }

    public function test_execute_with_user_runs_query_and_sets_csv(): void
    {
        DB::table('transactions')->insert([
            'user_id' => 'u1',
            'type' => 'IN',
            'amount' => 100,
            'note' => 'n',
            'date' => '2025-01-01',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $parsed = FinanceAnalyzeResult::parse("header\nSELECT sum(amount) FROM transactions; alasan");
        $out = FinanceAnalyzeService::executeWithUser($parsed, 'u1');
        $csv = $out->dataOf(0);
        $this->assertNotNull($csv);
        $this->assertStringContainsString('sum(amount)', (string) $csv);
    }
}

