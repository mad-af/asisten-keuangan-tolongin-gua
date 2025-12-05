<?php

namespace App\Http\Controllers;

use App\Services\TransactionService;
use App\Services\UserService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct(protected TransactionService $transactions, protected UserService $users) {}

    public function index(Request $request)
    {
        $token = $request->cookie('user_token') ?? (string) $request->input('token', '');
        $user = $this->users->getByToken($token);
        if (! $user) return response()->json(['error' => 'Unauthorized'], 401);
        $limit = (int) $request->query('limit', 200);
        return response()->json($this->transactions->listForUser($user, $limit));
    }

    public function cashflow(Request $request)
    {
        $token = $request->cookie('user_token') ?? (string) $request->input('token', '');
        $user = $this->users->getByToken($token);
        if (! $user) return response()->json(['error' => 'Unauthorized'], 401);
        return response()->json($this->transactions->cashflowByUser($user));
    }
}

