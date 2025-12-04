<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class AgentService
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected AgentChatService $agentChat, protected AgentToolService $agentTool) {}

    public function chat(string $message): array
    {
        try {
            $orchestrator = $this->agentChat->agentOrchestrator($message);

            $data = $this->agentTool->call($orchestrator);
            
        } catch (\Throwable $th) {
            Log::error('AgentService::chat error', ['exception' => $th->getMessage(), 'trace' => $th->getTraceAsString()]);
            return ['data' => null];
        } finally {
            return ['data' => $data];
        }
    }

}
