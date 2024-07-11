<?php

namespace App\Http\Controllers\Communications;

use App\Http\Controllers\Controller;
use App\Models\Communications\Conversation;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    /**
     * Get the current user's conversations
     * 
     * @group Messages
     * @response 200 {
     *   "id": 1,
     *   "user_one_id": 1,
     *   "user_two_id": 2,
     *   "created_at": "2024-07-06T00:00:00.000000Z",
     *   "updated_at": "2024-07-06T00:00:00.000000Z",
     *   "other_user_name": "Jane Doe",
     *   "other_user_image": "http://example.com/path/to/image.jpg",
     *   "last_message": {
     *     "id": 1,
     *     "conversation_id": 1,
     *     "sender_id": 1,
     *     "message": "Hello",
     *     "created_at": "2024-07-06T00:00:00.000000Z",
     *     "updated_at": "2024-07-06T00:00:00.000000Z",
     *     "sender": {
     *       "id": 1,
     *       "name": "John Doe"
     *     }
     *   }
     * }
     */
    public function index()
    {
        $userId = auth()->id();

        $conversations = Conversation::with(['messages.sender'])
            ->where('user_one_id', $userId)
            ->orWhere('user_two_id', $userId)
            ->get()
            ->map(function ($conversation) {
                return [
                    'id' => $conversation->id,
                    'user_one_id' => $conversation->user_one_id,
                    'user_two_id' => $conversation->user_two_id,
                    'created_at' => $conversation->created_at,
                    'updated_at' => $conversation->updated_at,
                    'other_user_name' => $conversation->other_user_name,
                    'other_user_image' => $conversation->other_user_image,
                    'last_message' => $conversation->last_message,
                ];
            });

        return response()->json($conversations);
    }

    /**
     * Create a new conversation
     * 
     * @group Messages
     * @bodyParam user_one_id int required The ID of the first user. Example: 1
     * @bodyParam user_two_id int required The ID of the second user. Example: 2
     * @response 201 {
     *   "id": 1,
     *   "user_one_id": 1,
     *   "user_two_id": 2,
     *   "created_at": "2024-07-06T00:00:00.000000Z",
     *   "updated_at": "2024-07-06T00:00:00.000000Z"
     * }
     * @response 200 {
     *   "message": "Conversation already exists",
     *   "conversation": {
     *     "id": 1,
     *     "user_one_id": 1,
     *     "user_two_id": 2,
     *     "created_at": "2024-07-06T00:00:00.000000Z",
     *     "updated_at": "2024-07-06T00:00:00.000000Z"
     *   }
     * }
     */
    public function store(Request $request)
    {
        $userOneId = $request->user_one_id;
        $userTwoId = $request->user_two_id;

        $existingConversation = Conversation::where(function ($query) use ($userOneId, $userTwoId) {
            $query->where('user_one_id', $userOneId)->where('user_two_id', $userTwoId);
        })->orWhere(function ($query) use ($userOneId, $userTwoId) {
            $query->where('user_one_id', $userTwoId)->where('user_two_id', $userOneId);
        })->first();

        if ($existingConversation) {
            return response()->json([
                'message' => 'Conversation already exists',
                'conversation' => $existingConversation
            ], 200);
        }

        $conversation = Conversation::create([
            'user_one_id' => $userOneId,
            'user_two_id' => $userTwoId,
        ]);

        return response()->json($conversation, 201);
    }
}
