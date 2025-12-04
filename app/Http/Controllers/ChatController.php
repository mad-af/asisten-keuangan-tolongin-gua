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

    public function index()
    {

        return Inertia::render('Chat/Index');
    }

    public function sendMessage(Request $request)
    {
        $this->messageService->send($request);

        return redirect()->route('chat.index');
    }

    public function getMessages($device_id)
    {
        return Message::where('from', $device_id)->orWhere('to', $device_id)->orderBy('created_at', 'asc')->get();
    }
}
