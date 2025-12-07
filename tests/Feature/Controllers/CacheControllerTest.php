<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;

class CacheControllerTest extends TestCase
{
    public function test_clear_returns_flushed_true(): void
    {
        $res = $this->get('/api/cache/clear');
        $res->assertStatus(200);
        $res->assertJson(['flushed' => true]);
    }
}

