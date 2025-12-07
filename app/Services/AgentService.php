<?php

namespace App\Services;

use App\Models\OrchestratorMemory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AgentService
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected AgentChatService $agentChat, protected AgentToolService $agentTool, protected MemoryService $memoryService) {}

    public function chat(string $message): ?AgentToolCallResult
    {
        try {

            $token = request()->cookie('user_token') ?? (string) request()->input('token', '');
            /** @var UserService $users */
            $users = app(UserService::class);
            $user = $users->getByToken($token);
            $resolvedUserId = $user ? (string) $user->id : null;

            if ($resolvedUserId) {
                try {
                    $this->memoryService->summarizeAndStoreByUserId($resolvedUserId);
                } catch (\Throwable $e) {
                    Log::warning('memory_summarize_trigger_error', ['user_id' => $resolvedUserId, 'error' => $e->getMessage()]);
                }
            }

            $premessages = null;
            if ($resolvedUserId) {
                $memory = OrchestratorMemory::where('user_id', operator: $resolvedUserId)->first();
                if ($memory && $memory->content) {
                    $premessages = [
                        [
                            'role' => 'system',
                            'content' => (string) $memory->content,
                        ],
                    ];
                }
            }
            $orchestrator = $this->agentChat->agentOrchestrator($message, $premessages, true);

            $data = $this->agentTool->call($orchestrator, $resolvedUserId);
        } catch (\Throwable $th) {
            Log::error('AgentService::chat error', ['exception' => $th->getMessage(), 'trace' => $th->getTraceAsString()]);

            return null;
        } finally {
            return $data;
        }
    }
}
