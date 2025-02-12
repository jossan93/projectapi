<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;

use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function chat(Request $request){

        $responseData = Http::post('http://localhost:11434/api/generate', [
        'model' => 'mistral',
        'prompt' => $request->message,
        'stream' => false
       ]);
       return response()->json($responseData->json());
       }


    
}