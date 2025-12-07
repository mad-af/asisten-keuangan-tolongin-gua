<?php

namespace App\Services;

use App\Models\Message;
use Illuminate\Support\Facades\Log;

class AgentService
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected AgentChatService $agentChat, protected AgentToolService $agentTool) {}

    public function chat(string $message, ): ?AgentToolCallResult
    {
        try {
            $token = request()->cookie('user_token') ?? (string) request()->input('token', '');
            /** @var UserService $users */
            $users = app(UserService::class);
            $user = $users->getByToken($token);
            $resolvedUserId = $user ? (string) $user->id : null;

            $premessages = $resolvedUserId ? Message::lastTenRoleContentByUser($resolvedUserId) : null;
            $orchestrator = $this->agentChat->agentOrchestrator($message, $premessages);

            $data = $this->agentTool->call($orchestrator, $resolvedUserId);
        } catch (\Throwable $th) {
            Log::error('AgentService::chat error', ['exception' => $th->getMessage(), 'trace' => $th->getTraceAsString()]);

            return null;
        } finally {
            return $data;
        }
    }
}
