<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatbotController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function() {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

route::post('/chat', [ChatbotController::class, 'chat']);
// Route::middleware(['auth:sanctum'])->post('/chat', [ChatbotController::class, 'chat']);

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