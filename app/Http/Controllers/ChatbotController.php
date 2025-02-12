<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ChatHistory;
use App\Models\Session;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\DB;

class ChatbotController extends Controller
{
    public function chat(Request $request) {
        $user_id = Auth::check() ? Auth::id() : null;
        $session_id = $request->session_id ?? (string) Uuid::uuid4();

        $session = Session::where('session_id', $session_id)->first();

        if (!$session) {
            Session::create([
                'session_id' => $session_id,
                'user_id' => $user_id,
                'last_activity' => time(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'payload' => json_encode([]),
            ]);
        }
        // Hämta tidigare konversationer
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

        // Lägg till senaste meddelandet
        $messages = array_merge($previousMessages, [['role' => 'user', 'content' => $request->message]]);

        // Skicka till LLM
        $responseData = Http::post('http://localhost:11434/api/chat', [
            'model' => 'mistral',
            'messages' => $messages,
            'stream' => false
        ]);

        if ($responseData->successful()) {
            // Get the response as an array
            $responseArray = $responseData->json();

            // Directly access the message content
            if (isset($responseArray['message']['content'])) {
                $bot_response = $responseArray['message']['content'];
            } else {
                $bot_response = 'Sorry, I encountered an issue.';
            }
        } else {
            // If the API request fails
            $bot_response = 'Sorry, I encountered an issue.';
        }
        // Spara i historiken
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