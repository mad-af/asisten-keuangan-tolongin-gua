<?php

namespace App\Http\Controllers;

use App\Services\AgentChatService;
use Illuminate\Http\Request;

class KolosalChatController extends Controller
{
    public function __construct(protected AgentChatService $agent) {}

    public function completions(Request $request)
    {
        $mode = (string) $request->input('mode', 'orchestrator');
        $message = $request->input('message');

        $message = (string) ($message ?? '');

        $result = $mode === 'persona'
            ? $this->agent->agentPersonaChat($message === '' ? null : $message)
            : $this->agent->agentOrchestratorChat($message);

        return response()->json([
            'ok' => $result->isOk(),
            'status' => $result->status,
            'response' => $result->getData(),
            'error' => $result->getError(),
            'meta' => $result->meta,
        ], $result->status);
    }
}
