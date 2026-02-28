<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ChatSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

        // Call Ollama with Streaming
        $ollamaUrl = rtrim(env('OLLAMA_URL', 'http://127.0.0.1:11434'), '/');
        
        return new StreamedResponse(function () use ($ollamaUrl, $request, $history, $session) {
            $client = new \GuzzleHttp\Client();
            try {
                $response = $client->post($ollamaUrl . '/api/chat', [
                    'json' => [
                        'model' => $request->model,
                        'messages' => $history,
                        'stream' => true,
                    ],
                    'stream' => true,
                    'timeout' => 120,
                ]);

                $body = $response->getBody();
                $fullContent = '';
                $buffer = '';

                while (!$body->eof()) {
                    $buffer .= $body->read(1024);
                    
                    while (($pos = strpos($buffer, "\n")) !== false) {
                        $line = substr($buffer, 0, $pos);
                        $buffer = substr($buffer, $pos + 1);
                        
                        if (trim($line) !== '') {
                            $data = json_decode($line, true);
                            if (isset($data['message']['content'])) {
                                $fullContent .= $data['message']['content'];
                            }
                            echo "data: " . $line . "\n\n";
                            ob_flush();
                            flush();
                        }
                    }
                }

                if (trim($buffer) !== '') {
                    $data = json_decode($buffer, true);
                    if (isset($data['message']['content'])) {
                        $fullContent .= $data['message']['content'];
                    }
                    echo "data: " . $buffer . "\n\n";
                    ob_flush();
                    flush();
                }

                // After stream finishes, save to database
                if (!empty($fullContent)) {
                    $session->messages()->create([
                        'role' => 'assistant',
                        'content' => $fullContent,
                    ]);

                    if ($session->name === 'New Chat' && count($history) <= 2) {
                        $session->update(['name' => substr($request->content, 0, 30) . '...']);
                    }
                }

            } catch (\Exception $e) {
                echo "data: " . json_encode(['error' => 'Could not connect to Ollama: ' . $e->getMessage()]) . "\n\n";
                ob_flush();
                flush();
            }
        }, 200, [
            'Cache-Control' => 'no-cache',
            'Content-Type' => 'text/event-stream',
            'X-Accel-Buffering' => 'no'
        ]);
    }
}
