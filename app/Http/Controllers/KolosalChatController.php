<?php

namespace App\Http\Controllers;

use App\Services\AgentService;
use Illuminate\Http\Request;

class KolosalChatController extends Controller
{
    public function __construct(protected AgentService $agent) {}

    public function completions(Request $request)
    {
        $message = (string) ($request->input('message') ?? '');

        $result = $this->agent->chat($message);

        return response()->json($result, $result['status'] ?? 200);
    }
}
