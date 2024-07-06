<?php

namespace App\Http\Controllers\Communications;

use App\Http\Controllers\Controller;
use App\Models\Communications\Conversation;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    /**
     * Get a list of conversations
     * 
     * @group Conversations
     * @response 200 {
     *   "id": 1,
     *   "user_one_id": 1,
     *   "user_two_id": 2,
     *   "created_at": "2024-07-06T00:00:00.000000Z",
     *   "updated_at": "2024-07-06T00:00:00.000000Z",
     *   "messages": [
     *     {
     *       "id": 1,
     *       "conversation_id": 1,
     *       "sender_id": 1,
     *       "message": "Hello",
     *       "created_at": "2024-07-06T00:00:00.000000Z",
     *       "updated_at": "2024-07-06T00:00:00.000000Z",
     *       "sender": {
     *         "id": 1,
     *         "name": "John Doe"
     *       }
     *     }
     *   ]
     * }
     */
    public function index()
    {
        $conversations = Conversation::with('messages.sender')->get();
        return response()->json($conversations);
    }

    /**
     * Create a new conversation
     * 
     * @group Conversations
     * @bodyParam user_one_id int required The ID of the first user. Example: 1
     * @bodyParam user_two_id int required The ID of the second user. Example: 2
     * @response 201 {
     *   "id": 1,
     *   "user_one_id": 1,
     *   "user_two_id": 2,
     *   "created_at": "2024-07-06T00:00:00.000000Z",
     *   "updated_at": "2024-07-06T00:00:00.000000Z"
     * }
     */
    public function store(Request $request)
    {
        $conversation = Conversation::create([
            'user_one_id' => $request->user_one_id,
            'user_two_id' => $request->user_two_id,
        ]);

        return response()->json($conversation, 201);
    }
}
