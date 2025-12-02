<?php

namespace App\Http\Controllers;

use App\Services\KolosalApiClient;
use Illuminate\Http\Request;

class KolosalChatController extends Controller
{
    public function __construct(protected KolosalApiClient $client)
    {
    }

    public function completions(Request $request)
    {
        $payload = $request->only(['model', 'messages', 'max_tokens', 'temperature', 'tools', 'response_format']);
        $result = $client = $this->client->chatCompletions($payload);
        return response()->json($result, $result['status']);
    }
}
