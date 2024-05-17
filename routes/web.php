<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
Route::get('/edit', [ChatController::class, 'editContext'])->name('chat.edit');
Route::post('/update', [ChatController::class, 'updateContext'])->name('chat.update');

Route::post('/chat', [ChatController::class, 'processChat'])->name('chat.process');

Route::post('/twillio-webhook', [ChatController::class, 'twillioWebhook']);


