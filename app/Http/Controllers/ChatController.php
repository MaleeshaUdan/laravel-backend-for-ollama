<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ChatSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function index()
    {
        $sessions = Auth::user()->chatSessions()->latest()->get();
        return view('chat', compact('sessions'));
    }

    public function createSession(Request $request)
    {
        $request->validate(['name' => 'nullable|string|max:255']);
        
        $session = Auth::user()->chatSessions()->create([
            'name' => $request->name ?? 'New Chat',
        ]);

        return response()->json($session);
    }

    public function getSessionMessages(ChatSession $session)
    {
        if ($session->user_id !== Auth::id()) {
            abort(403);
        }
        
        return response()->json($session->messages()->oldest()->get());
    }

    public function sendMessage(Request $request, ChatSession $session)
    {
        if ($session->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'content' => 'required|string',
            'model' => 'required|string'
        ]);

        // Save user message
        $session->messages()->create([
            'role' => 'user',
            'content' => $request->content,
        ]);

        // Build history for Ollama
        $history = $session->messages()->oldest()->get()->map(function($msg) {
            return [
                'role' => $msg->role,
                'content' => $msg->content,
            ];
        })->toArray();

        // Call Ollama
        try {
            $response = Http::timeout(60)->post('http://localhost:11434/api/chat', [
                'model' => $request->model,
                'messages' => $history,
                'stream' => false,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $replyContent = $data['message']['content'] ?? 'No response content';

                $aiMsg = $session->messages()->create([
                    'role' => 'assistant',
                    'content' => $replyContent,
                ]);

                // Update session name if it's "New Chat"
                if ($session->name === 'New Chat' && count($history) <= 2) {
                    $session->update(['name' => substr($request->content, 0, 30) . '...']);
                }

                return response()->json($aiMsg);
            }

            return response()->json(['error' => 'Ollama API Error: ' . $response->body()], 500);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Could not connect to Ollama: ' . $e->getMessage()], 500);
        }
    }
}
