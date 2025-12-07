<?php

namespace Tests\Unit\Services;

use App\Models\Message;
use App\Services\AgentChatService;
use App\Services\AgentToolService;
use App\Services\ChatResponse;
use App\Services\FinanceAnalyzeService;
use App\Services\KolosalApiClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgentToolServiceTest extends TestCase
{
    use RefreshDatabase;

    private function stubAgentChatReturning(string $content): AgentChatService
    {
        $client = new class($content) extends KolosalApiClient {
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

        return new AgentChatService($client);
    }

    public function test_call_invokes_methods_and_coerces_types(): void
    {
        Message::create(['user_id' => 'u1', 'body' => 'hi', 'type' => 'user']);

        $agentChat = $this->stubAgentChatReturning('ok');
        $finance = new FinanceAnalyzeService();
        $svc = new AgentToolService($agentChat, $finance);

        $items = [
            ['function' => 'transaction_in', 'param' => ['amount' => '100', 'note' => 'n', 'date' => '2025-01-01']],
            ['function' => 'persona_chat', 'param' => ['reason' => 'r', 'premessages' => null]],
        ];

        $res = $svc->call($items, 'u1');
        $all = $res->all();
        $this->assertCount(2, $all);
        $this->assertSame(100, $all[0]['args'][0]);
        $this->assertSame('ok', $all[1]['result']);
    }
}

