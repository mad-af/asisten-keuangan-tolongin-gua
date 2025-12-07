<?php

namespace Tests\Feature\Controllers;

use App\Enums\MessageType;
use App\Models\Message;
use App\Services\AgentService;
use App\Services\AgentToolCallResult;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_message_creates_user_and_assistant_reply(): void
    {
        app()->instance(AgentService::class, new class extends AgentService {
            public function __construct() {}
            public function chat(string $message, ?string $userId = null): ?AgentToolCallResult {
                return new AgentToolCallResult([
                    ['function' => 'persona_chat', 'result' => 'ok']
                ]);
            }
        });

        $this->postJson('/api/chat/send', ['message' => 'hai', 'user_id' => 'u1'])->assertStatus(200);
        $rows = Message::where('user_id', 'u1')->orderBy('created_at')->get();
        $this->assertCount(2, $rows);
        $this->assertSame(MessageType::user, $rows[0]->type);
        $this->assertSame(MessageType::assistant, $rows[1]->type);
    }

    public function test_get_and_latest_and_fallback(): void
    {
        Message::create(['user_id' => 'u1', 'body' => 'h', 'type' => MessageType::user]);
        $this->get('/api/messages/u1')->assertStatus(200)->assertJsonCount(1);
        $this->get('/api/messages/u1/latest')->assertStatus(200)->assertJsonStructure(['id','user_id','body','type']);
        $this->postJson('/api/messages/u1/fallback')->assertStatus(201);
        $this->assertSame(2, Message::where('user_id', 'u1')->count());
    }
}

