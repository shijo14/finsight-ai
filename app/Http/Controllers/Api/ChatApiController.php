<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChatHistoryResource;
use App\Services\ChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ChatApiController extends Controller
{
    public function __construct(private readonly ChatbotService $chatbot)
    {
    }

    /**
     * Return the authenticated user's recent chat history (paginated).
     *
     * GET /api/chat
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->integer('per_page', 15);
        $perPage = max(1, min($perPage, 50));

        $history = $request->user()
            ->chatHistories()
            ->latest('created_at')
            ->paginate($perPage);

        return ChatHistoryResource::collection($history);
    }

    /**
     * Send a message to the chatbot and receive the AI response.
     *
     * POST /api/chat
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $chat = $this->chatbot->ask($request->user(), $validated['message']);

        return (new ChatHistoryResource($chat))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Show a single chat exchange owned by the authenticated user.
     *
     * GET /api/chat/{id}
     */
    public function show(Request $request, int $id): ChatHistoryResource
    {
        $chat = $request->user()->chatHistories()->findOrFail($id);

        return new ChatHistoryResource($chat);
    }

    /**
     * Delete a single chat exchange.
     *
     * DELETE /api/chat/{id}
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $chat = $request->user()->chatHistories()->findOrFail($id);
        $chat->delete();

        return response()->json(['message' => 'Chat deleted.']);
    }

    /**
     * Clear the authenticated user's entire chat history.
     *
     * DELETE /api/chat
     */
    public function clear(Request $request): JsonResponse
    {
        $deleted = $request->user()->chatHistories()->delete();

        return response()->json([
            'message' => 'Chat history cleared.',
            'deleted' => $deleted,
        ]);
    }
}
