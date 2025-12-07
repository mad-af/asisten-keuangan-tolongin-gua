<?php

namespace Tests\Unit\Services;

use App\Enums\MessageType;
use App\Models\Message;
use App\Services\AgentChatService;
use App\Services\AgentToolCallResult;
use App\Services\AgentToolService;
use App\Services\FinanceAnalyzeService;
use App\Services\KolosalApiClient;
use App\Services\MessageService;
use App\Services\AgentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class MessageServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_fallback_creates_assistant_message(): void
    {
        $svc = new MessageService(new class extends AgentService {
            public function __construct() {}
            public function chat(string $message, ?string $userId = null): ?AgentToolCallResult { return null; }
        });
        $msg = $svc->createFallbackByUserId('u1');
        $this->assertSame(MessageType::assistant, $msg->type);
    }

    public function test_send_by_user_persists_user_and_assistant_reply(): void
    {
        new AgentToolService(new AgentChatService(new class extends KolosalApiClient {}), new FinanceAnalyzeService());
        $agent = new class extends AgentService {
            public function __construct() {}
            public function chat(string $message, ?string $userId = null): ?AgentToolCallResult {
                return new AgentToolCallResult([
                    ['function' => 'persona_chat', 'result' => 'ok'],
                ]);
            }
        };
        $svc = new MessageService($agent);
        $req = new Request(['message' => 'hai', 'user_id' => 'u1']);
        $svc->sendByUser($req);
        $rows = Message::where('user_id', 'u1')->orderBy('created_at')->get();
        $this->assertCount(2, $rows);
        $this->assertSame(MessageType::user, $rows[0]->type);
        $this->assertSame(MessageType::assistant, $rows[1]->type);
        $this->assertSame('ok', $rows[1]->body);
    }
}

