<?php

namespace App\Http\Controllers;

use App\Services\ChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function __construct(private readonly ChatbotService $chatbot)
    {
    }

    public function index(): View
    {
        $history = auth()->user()->chatHistories()->oldest('created_at')->take(50)->get();

        return view('chat.index', compact('history'));
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
            'roast_mode' => ['nullable', 'boolean'],
        ]);

        $chatHistory = $this->chatbot->ask(auth()->user(), $validated['message'], $request->boolean('roast_mode'));

        // AJAX response
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success'  => true,
                'message'  => $chatHistory->message,
                'response' => $chatHistory->response,
            ]);
        }

        // Fallback for non-JS
        return back()->with('status', 'Your message was sent successfully.');
    }
}
