<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\KolosalChatController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Wellcome/Page');
});

Route::get('/choose-your-setup', function () {
    return Inertia::render('ChooseYourSetup/Page');
});

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
    Route::get('/chat', 'index')->name('chat.index');
    Route::get('/messages/{device_id}', 'getMessages')->name('chat.messages');
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
