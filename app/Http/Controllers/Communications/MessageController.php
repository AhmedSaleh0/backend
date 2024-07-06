<?php

namespace App\Http\Controllers\Communications;

use App\Http\Controllers\Controller;
use App\Models\Communications\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Get a list of messages in a conversation
     * 
     * @group Messages
     * @urlParam conversationId int required The ID of the conversation. Example: 1
     * @response 200 {
     *   "id": 1,
     *   "conversation_id": 1,
     *   "sender_id": 1,
     *   "message": "Hello",
     *   "created_at": "2024-07-06T00:00:00.000000Z",
     *   "updated_at": "2024-07-06T00:00:00.000000Z",
     *   "sender": {
     *     "id": 1,
     *     "name": "John Doe"
     *   }
     * }
     */
    public function index($conversationId)
    {
        $messages = Message::where('conversation_id', $conversationId)->with('sender')->get();
        return response()->json($messages);
    }

    /**
     * Send a new message in a conversation
     * 
     * @group Messages
     * @urlParam conversationId int required The ID of the conversation. Example: 1
     * @bodyParam sender_id int required The ID of the sender. Example: 1
     * @bodyParam message string required The message content. Example: Hello
     * @response 201 {
     *   "id": 1,
     *   "conversation_id": 1,
     *   "sender_id": 1,
     *   "message": "Hello",
     *   "created_at": "2024-07-06T00:00:00.000000Z",
     *   "updated_at": "2024-07-06T00:00:00.000000Z"
     * }
     */
    public function store(Request $request, $conversationId)
    {
        $message = Message::create([
            'conversation_id' => $conversationId,
            'sender_id' => $request->sender_id,
            'message' => $request->message,
        ]);

        return response()->json($message, 201);
    }
}
