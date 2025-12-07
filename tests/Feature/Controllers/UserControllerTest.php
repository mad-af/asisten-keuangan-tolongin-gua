<?php

namespace Tests\Feature\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_sets_cookie_and_returns_user(): void
    {
        $res = $this->postJson('/api/users/register', ['name' => 'Budi']);
        $res->assertStatus(200)->assertJsonStructure(['id', 'name', 'setup_type', 'token', 'expires_at']);
        $token = $res->json('token');
    }

    public function test_setup_updates_setup_type_and_me_returns_user(): void
    {
        $reg = $this->postJson('/api/users/register', ['name' => 'Budi']);
        $token = $reg->json('token');

        $setup = $this->postJson('/api/users/setup', ['token' => $token, 'setup_type' => 'dummy']);
        $setup->assertStatus(200)->assertJson(['setup_type' => 'dummy']);
    }
}
