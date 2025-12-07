<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;

class TestControllerTest extends TestCase
{
    public function test_index_renders_inertia(): void
    {
        $this->get('/test')->assertStatus(200);
    }
}
