<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatbotController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

route::post('/register', [AuthController::class, 'register']);
route::post('/login', [AuthController::class, 'login']);
route::get('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// route::post('/chat', [ChatbotController::class, 'chat']);
Route::middleware(['auth:sanctum'])->post('/chat', [ChatbotController::class, 'chat']);

route::post('/jsonTest', function (Request $request) {
    try {
        $request->validate([
        'jsonData' => 'required|string',
    ]);

   dump($request->jsonData);
   return response()->json(['response' => 'hello other side'], 200);    
    } catch(\Exception $e) {
        return response()->json(['response' => 'bad request'], 400);
    }
});