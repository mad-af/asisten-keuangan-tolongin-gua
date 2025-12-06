<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function register(Request $request, UserService $users)
    {
        $name = (string) $request->input('name', '');
        $result = $users->createNameOnly($name);
        $user = $result['user'];
        $token = $result['token'];
        $expiresAt = $result['expires_at'];
        $cookie = $users->makeAuthCookie($token);

        return response()
            ->json([
                'id' => $user->id,
                'name' => $user->name,
                'setup_type' => $user->setup_type,
                'token' => $token,
                'expires_at' => $expiresAt->toIso8601String(),
            ])
            ->withCookie($cookie);
    }

    public function setup(Request $request, UserService $users)
    {
        $token = $request->cookie('user_token') ?? (string) $request->input('token', '');
        $user = $users->getByToken($token);
        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $type = (string) $request->input('setup_type', '');
        $users->updateSetupType($user, $type);

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'setup_type' => $user->setup_type,
        ]);
    }

    public function me(Request $request, UserService $users)
    {
        $token = $request->cookie('user_token');
        $user = $users->getByToken($token);
        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'setup_type' => $user->setup_type,
        ]);
    }
}
