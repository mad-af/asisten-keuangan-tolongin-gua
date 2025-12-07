<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_name_only_creates_user_and_token(): void
    {
        $svc = new UserService();
        $out = $svc->createNameOnly(' Alice ');
        $this->assertInstanceOf(User::class, $out['user']);
        $this->assertIsString($out['token']);
    }

    public function test_update_setup_type_dummy_seeds_transactions(): void
    {
        $svc = new UserService();
        $u = User::create(['name' => 'n']);
        $svc->updateSetupType($u, 'dummy');
        $count = \App\Models\Transaction::where('user_id', $u->id)->count();
        $this->assertGreaterThan(0, $count);
    }
}

