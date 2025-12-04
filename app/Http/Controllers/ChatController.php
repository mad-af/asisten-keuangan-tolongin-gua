<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Message;
use App\Models\Transaction;
use App\Services\AiParse;
use App\Services\KolosalApiClient;
use App\Services\MessageService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChatController extends Controller
{

    public function __construct(protected MessageService $messageService) {}

    public function index(Request $request)
    {
        $device = null;
        $messages = [];

        if ($request->device_id) {
            $device = Device::find($request->device_id);

            if ($device) {
                $messages = Message::where('device_id', $device->id)
                    ->orderBy('created_at')
                    ->get();
            }
        }

        return Inertia::render('Chat/Index', [
            'device' => $device,
            'messages' => $messages,
        ]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'device_id' => 'required|string',
            'message' => 'required|string',
        ]);

        $result = $this->messageService->message($request);

        return redirect()->route('chat.index', [
            'device_id' => $result['device']->id,
        ]);
    }
}
