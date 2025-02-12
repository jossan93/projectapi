<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ChatHistory;
use Ramsey\Uuid\Uuid;

class ChatbotController extends Controller
{
    public function chat(Request $request){
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user_id = Auth::id();
        $session_id = $request->session_id;

        if (!$session_id) {
            $session_id = (string) Uuid::uuid4();
            $previousMessages = [];
        } else {
            
            $previousMessages = ChatHistory::where('user_id', $user_id)
                ->where('session_id', $session_id)
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(fn($chat) => [
                    ['role' => 'user', 'content' => $chat->user_message],
                    ['role' => 'assistant', 'content' => $chat->bot_response],
                ])
                ->flatten(1)
                ->toArray();
        }

        $messages = array_merge($previousMessages, [
            ['role' => 'user', 'content' => $request->message]
        ]);

        $responseData = Http::post('http://localhost:11434/api/generate', [
            'model' => 'mistral',
            'prompt' => $request->message,
            'stream' => false
        ]);

        $bot_response = $responseData->json()['response'] ?? 'Sorry, I encountered an issue.';

        ChatHistory::create([
            'user_id' => $user_id,
            'session_id' => $session_id,
            'user_message' => $request->message,
            'bot_response' => $bot_response
        ]);


        return response()->json([
            'session_id' => $session_id, 
            'message' => $bot_response
        ]);
    }
}