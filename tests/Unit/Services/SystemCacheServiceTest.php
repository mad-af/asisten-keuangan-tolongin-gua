<?php

namespace Tests\Unit\Services;

use App\Services\SystemCacheService;
use Tests\TestCase;

class SystemCacheServiceTest extends TestCase
{
    public function test_clear_all_returns_true(): void
    {
        $svc = new SystemCacheService();
        $out = $svc->clearAll();
        $this->assertTrue($out['flushed']);
    }
}

