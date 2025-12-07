<?php

namespace Tests\Feature\Controllers;

use App\Services\AgentService;
use App\Services\AgentToolCallResult;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KolosalChatControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_completions_returns_items_and_persona_chat(): void
    {
        app()->instance(AgentService::class, new class extends AgentService {
            public function __construct() {}
            public function chat(string $message, ?string $userId = null): ?AgentToolCallResult
            {
                return new AgentToolCallResult([
                    ['index' => 0, 'function' => 'transaction_in', 'args' => [100,'n','2025-01-01'], 'result' => ['ok' => true]],
                    ['index' => 1, 'function' => 'persona_chat', 'args' => ['r', []], 'result' => 'hai']
                ]);
            }
        });

        $res = $this->postJson('/api/kolosal/chat', ['message' => 'halo']);
        $res->assertStatus(200)->assertJsonStructure(['persona_chat','items']);
        $this->assertSame('hai', $res->json('persona_chat'));
        $this->assertCount(2, $res->json('items'));
    }
}

