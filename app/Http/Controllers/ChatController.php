<?php

namespace App\Http\Controllers;

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

    public function sendMessageByUser(Request $request)
    {
        return $this->messageService->sendByUser($request);
    }

    public function getMessagesByUserId($user_id)
    {
        return $this->messageService->getByUserId(userId: $user_id);
    }

    public function getLatestMessageByUserId($user_id)
    {
        $latest = $this->messageService->latestByUserId(userId: $user_id);
        if ($latest) {
            return response()->json($latest);
        }

        return response()->json([]);
    }
}
