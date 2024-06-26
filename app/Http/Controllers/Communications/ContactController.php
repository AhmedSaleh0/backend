<?php

namespace App\Http\Controllers\Communications;

use App\Models\Communications\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class ContactController extends Controller
{
    /**
     * Send a contact message.
     *
     * @group Communications
     * @bodyParam name string required The name of the sender. Example: John Doe
     * @bodyParam email string required The email of the sender. Example: john@example.com
     * @bodyParam message string required The message content. Example: Hello, I have a question about...
     * @response 200 {
     *  "message": "Message sent successfully!"
     * }
     * @response 422 {
     *  "errors": {
     *    "name": [
     *      "The name field is required."
     *    ],
     *    "email": [
     *      "The email field is required."
     *    ],
     *    "message": [
     *      "The message field is required."
     *    ]
     *  }
     * }
     */
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        Contact::create($request->only('name', 'email', 'message'));

        return response()->json(['message' => 'Message sent successfully!'], 200);
    }
}
