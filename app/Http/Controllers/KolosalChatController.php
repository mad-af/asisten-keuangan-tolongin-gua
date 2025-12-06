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

        $items = $result?->all() ?? [];
        $mapped = array_map(function ($it) {
            $res = $it['result'] ?? null;
            if ($res instanceof \Illuminate\Database\Eloquent\Model) {
                $resData = $res->toArray();
            } elseif (is_array($res)) {
                $resData = $res;
            } elseif (is_object($res)) {
                $resData = method_exists($res, 'toArray') ? $res->toArray() : json_decode(json_encode($res), true);
            } else {
                $resData = $res;
            }

            return [
                'index' => $it['index'] ?? null,
                'function' => $it['function'] ?? null,
                'args' => $it['args'] ?? [],
                'error' => $it['error'] ?? null,
                'result' => $resData,
            ];
        }, $items);

        return response()->json([
            'persona_chat' => $result?->personaChat(),
            'items' => $mapped,
        ], 200);
    }
}
