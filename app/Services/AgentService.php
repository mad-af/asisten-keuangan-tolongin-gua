<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class AgentService
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected AgentChatService $agentChat, protected AgentToolService $agentTool) {}

    public function chat(string $message, ?string $userId = null): ?AgentToolCallResult
    {
        try {
            $orchestrator = $this->agentChat->agentOrchestrator($message);

            $data = $this->agentTool->call($orchestrator, $userId);
        } catch (\Throwable $th) {
            Log::error('AgentService::chat error', ['exception' => $th->getMessage(), 'trace' => $th->getTraceAsString()]);
            return null;
        } finally {
            return $data;
        }
    }

}
