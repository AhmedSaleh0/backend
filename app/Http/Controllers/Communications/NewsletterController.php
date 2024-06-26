<?php

namespace App\Http\Controllers\Communications;

use Illuminate\Http\Request;
use App\Models\Communications\NewsletterSubscription;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class NewsletterController extends Controller
{
    /**
     * Subscribe to the newsletter.
     *
     * @group Communications
     * @bodyParam email string required The email address to subscribe. Example: john@example.com
     * @bodyParam list string required The list to subscribe to. Example: weekly-updates
     * @response 200 {
     *  "message": "Subscribed successfully!"
     * }
     * @response 409 {
     *  "error": "Email already subscribed."
     * }
     * @response 422 {
     *  "errors": {
     *    "email": [
     *      "The email field is required."
     *    ],
     *    "list": [
     *      "The list field is required."
     *    ]
     *  }
     * }
     */
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:newsletter_subscriptions,email',
            'list' => 'required',
        ]);

        if ($validator->fails()) {
            if ($validator->errors()->has('email')) {
                return response()->json(['error' => 'Email already subscribed.'], 409);
            }
            return response()->json(['errors' => $validator->errors()], 422);
        }

        NewsletterSubscription::create([
            'email' => $request->email,
            'list' => $request->list,
        ]);

        return response()->json(['message' => 'Subscribed successfully!'], 200);
    }

    /**
     * Unsubscribe from the newsletter.
     *
     * @group Communications
     * @bodyParam email string required The email address to unsubscribe. Example: john@example.com
     * @response 200 {
     *  "message": "Unsubscribed successfully!"
     * }
     * @response 422 {
     *  "errors": {
     *    "email": [
     *      "The email field is required."
     *    ]
     *  }
     * }
     */
    public function unsubscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:newsletter_subscriptions,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $subscription = NewsletterSubscription::where('email', $request->email)->first();
        $subscription->delete();

        return response()->json(['message' => 'Unsubscribed successfully!'], 200);
    }
}
