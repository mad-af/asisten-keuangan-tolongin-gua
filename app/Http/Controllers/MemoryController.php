<?php

namespace App\Http\Controllers;

use App\Services\MemoryService;
use Illuminate\Http\Request;

class MemoryController extends Controller
{
    public function __construct(protected MemoryService $memoryService) {}

    public function summarizeByUserId(Request $request, $user_id)
    {
        $memory = $this->memoryService->summarizeAndStoreByUserId(userId: $user_id);

        if (!$memory) {
            return response()->json([
                'error' => 'Memory not found',
            ], 404);
        }

        return response()->json([
            'user_id' => $memory->user_id,
            'content' => $memory->content,
            'metadata' => $memory->metadata,
        ]);
    }
}

