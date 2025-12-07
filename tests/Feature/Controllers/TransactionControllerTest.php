<?php

namespace Tests\Feature\Controllers;

use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionControllerTest extends TestCase
{
    use RefreshDatabase;

    private function registerUser(string $name): array
    {
        $res = $this->postJson('/api/users/register', ['name' => $name]);
        $res->assertStatus(200);

        return ['token' => $res->json('token'), 'id' => $res->json('id')];
    }

    public function test_index_requires_auth_and_returns_rows(): void
    {
        $this->get('/api/transactions')->assertStatus(401);

        $u = $this->registerUser('Caca');
        Transaction::createIn($u['id'], 100, 'x', '2025-01-01');
        $res = $this->get('/api/transactions?limit=50&token='.$u['token']);
        $res->assertStatus(200)->assertJsonCount(1);
    }

    public function test_cashflow_and_stats_month(): void
    {
        $u = $this->registerUser('Didi');
        Transaction::createIn($u['id'], 10, 'a', '2025-01-01');
        Transaction::createOut($u['id'], 5, 'b', '2025-01-01');

        $cf = $this->get('/api/transactions/cashflow?token='.$u['token']);
        $cf->assertStatus(200)->assertJsonStructure(['labels', 'inData', 'outData']);

        $sm = $this->get('/api/transactions/stats-month?month=2025-01&token='.$u['token']);
        $sm->assertStatus(200)->assertJson(['month' => '2025-01']);
    }
}
