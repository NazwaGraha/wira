<?php

use App\Http\Controllers\WiraAgentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group.
|
*/

Route::post('/wira-agent/chat', [WiraAgentController::class, 'chat'])->name('api.wira-agent.chat');
