<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class SystemCacheService
{
    public function clearAll(): array
    {
        $ok = false;
        try {
            Cache::flush();
            $ok = true;
        } catch (\Throwable $e) {
            $ok = false;
        }

        return [
            'flushed' => $ok,
        ];
    }
}

