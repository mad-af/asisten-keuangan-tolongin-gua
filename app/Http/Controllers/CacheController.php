<?php

namespace App\Http\Controllers;

use App\Services\SystemCacheService;

class CacheController extends Controller
{
    public function __construct(protected SystemCacheService $cacheService) {}

    public function clear()
    {
        $result = $this->cacheService->clearAll();
        return response()->json($result, 200);
    }
}

