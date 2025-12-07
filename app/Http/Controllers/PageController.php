<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PageController extends Controller
{
    public function index(Request $request, UserService $users)
    {
        $token = $request->cookie('user_token');
        $user = $users->getByToken($token);
        if ($user) {
            return redirect()->route('choose-your-setup.index');
        }

        return Inertia::render('Wellcome/Page');
    }

    public function chooseYourSetup(Request $request, UserService $users)
    {
        $token = $request->cookie('user_token');
        $user = $users->getByToken($token);
        if ($user && $user->setup_type === null) {
            return redirect()->route('chat.index');
        }

        return Inertia::render('ChooseYourSetup/Page');
    }

    public function chat()
    {
        return Inertia::render('Chat/Page');
    }

    public function transactions()
    {
        return Inertia::render('Transaction/Page');
    }

    public function enter(Request $request)
    {
        $name = $request->input('name');
        if (is_string($name) && $name !== '') {
            session(['display_name' => $name]);
        }

        return redirect()->route('chat.index');
    }
}

