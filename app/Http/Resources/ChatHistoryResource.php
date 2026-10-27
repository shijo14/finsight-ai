<?php

namespace App\Http\Resources;

use App\Models\ChatHistory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ChatHistory
 */
class ChatHistoryResource extends JsonResource
{
    /**
     * Transform a chat history record into its API representation.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'message'    => $this->message,
            'response'   => $this->response,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
