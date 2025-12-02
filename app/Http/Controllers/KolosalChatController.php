<?php

namespace App\Http\Controllers;

use App\Services\KolosalApiClient;
use Illuminate\Http\Request;

class KolosalChatController extends Controller
{
    public function __construct(protected KolosalApiClient $client) {}

    public function completions(Request $request)
    {
        $payload = $request->only(['messages', 'max_tokens']);
        $result = $this->client->chatCompletions($payload);

        return response()->json([
            'ok' => $result->isOk(),
            'status' => $result->status,
            'response' => $result->getData(),
            'error' => $result->getError(),
            'meta' => $result->meta,
        ], $result->status);
    }
}
