<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\KolosalChatController;
use App\Http\Controllers\TestController;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function (Request $request, UserService $users) {
    $token = $request->cookie('user_token');
    $user = $users->getByToken($token);
    if ($user) {
        return redirect()->route('choose-your-setup.index');
    }

    return Inertia::render('Wellcome/Page');
})->name('wellcome.index');

Route::get('/choose-your-setup', function (Request $request, UserService $users) {
    $token = $request->cookie('user_token');
    $user = $users->getByToken($token);
    if ($user && $user->setup_type === null) {
        return redirect()->route('chat.index');
    }

    return Inertia::render('ChooseYourSetup/Page');
})->name('choose-your-setup.index');

Route::get('/chat', function () {
    return Inertia::render('Chat/Page');
})->name('chat.index');

Route::get('/transactions', function () {
    return Inertia::render('Transaction/Page');
})->name('transactions.index');

Route::post('/enter', function () {
    $name = request('name');
    if (is_string($name) && $name !== '') {
        session(['display_name' => $name]);
    }

    return redirect()->route('chat.index');
})->name('enter');

Route::get('/test', [TestController::class, 'index'])->name('test.index');
Route::post('/kolosal/chat', [KolosalChatController::class, 'completions'])->name('kolosal.chat');

Route::controller(ChatController::class)->group(function () {
    // Route::get('/chat', 'index')->name('chat.index');
    Route::get('/messages/{user_id}', 'getMessages')->name('chat.messages');
    Route::post('/chat/send', 'sendMessage')->name('chat.send');
});

Route::post('/setup/select', function () {
    $option = request('option');
    if ($option === 'dummy') {
        session(['account_mode' => 'dummy', 'display_name' => session('display_name', 'Demo User')]);

        return redirect()->route('chat.index');
    }
    session(['account_mode' => 'new']);

    return redirect('/');
})->name('setup.select');
