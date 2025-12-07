<?php

namespace Tests\Unit\Services;

use App\Services\AgentChatService;
use App\Services\ChatResponse;
use App\Services\FinanceAnalyzeService;
use App\Services\KolosalApiClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgentChatServiceTest extends TestCase
{
    use RefreshDatabase;

    private function fakeClientReturning(string $content): KolosalApiClient
    {
        return new class($content) extends KolosalApiClient
        {
            public function __construct(private string $c) {}

            public function chatCompletions($request): ChatResponse
            {
                return ChatResponse::success(200, [
                    'choices' => [
                        ['message' => ['role' => 'assistant', 'content' => $this->c]],
                    ],
                ]);
            }
        };
    }

    public function test_agent_orchestrator_decodes_response(): void
    {
        $client = $this->fakeClientReturning('transaction_in [amount:100; note:x; date:2025-01-01]');
        $svc = new AgentChatService($client);
        $items = $svc->agentOrchestrator('hi');
        $this->assertCount(1, $items);
        $this->assertSame('transaction_in', $items[0]['function']);
        $this->assertSame(100, $items[0]['param']['amount']);
    }

    public function test_agent_finance_analyze_parses_queries(): void
    {
        $client = $this->fakeClientReturning("header\nSELECT * FROM transactions; alasan");
        $svc = new AgentChatService($client);
        new FinanceAnalyzeService;
        $res = $svc->agentFinanceAnalyze('ctx');
        $this->assertCount(1, $res->all());
        $this->assertSame('SELECT * FROM transactions', $res->sqls()[0]);
    }
}
