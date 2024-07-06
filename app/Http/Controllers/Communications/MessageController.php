<?php

namespace App\Http\Controllers\Communications;

use App\Http\Controllers\Controller;
use App\Models\Communications\Conversation;
use App\Models\Communications\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Get a list of messages in a conversation
     * 
     * @group Messages
     * @bodyParam conversation_id int required The ID of the conversation. Example: 1
     * @response 200 {
     *   "id": 1,
     *   "conversation_id": 1,
     *   "sender_id": 1,
     *   "message": "Hello",
     *   "is_sender": true,
     *   "created_at": "2024-07-06T00:00:00.000000Z",
     *   "updated_at": "2024-07-06T00:00:00.000000Z",
     *   "sender": {
     *     "id": 1,
     *     "name": "John Doe"
     *   }
     * }
     */
    public function index(Request $request)
    {
        $conversationId = $request->conversation_id;

        $messages = Message::where('conversation_id', $conversationId)
            ->with('sender')
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'conversation_id' => $message->conversation_id,
                    'sender_id' => $message->sender_id,
                    'message' => $message->message,
                    'is_sender' => $message->is_sender,
                    'created_at' => $message->created_at,
                    'updated_at' => $message->updated_at,
                    'sender' => [
                        'id' => $message->sender->id,
                        'name' => $message->sender->name,
                    ]
                ];
            });

        return response()->json($messages);
    }

    
    /**
     * Send a new message in a conversation
     * 
     * @group Messages
     * @bodyParam conversation_id int required The ID of the conversation. Example: 1
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
     * @response 403 {
     *   "message": "Unauthorized"
     * }
     */
    public function store(Request $request)
    {
        $conversation = Conversation::findOrFail($request->conversation_id);
        $userId = Auth::id();

        if ($conversation->user_one_id !== $userId && $conversation->user_two_id !== $userId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $message = Message::create([
            'conversation_id' => $request->conversation_id,
            'sender_id' => $request->sender_id,
            'message' => $request->message,
        ]);

        return response()->json($message, 201);
    }
}