<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    //
   $responseData = Http::post('http://localhost:11434/api/generate', [
    'model' => 'mistral',
    'prompt' => $request->message,
    'stream' => false
    ])
}
