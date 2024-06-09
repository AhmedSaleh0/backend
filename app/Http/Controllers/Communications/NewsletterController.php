<?php

namespace App\Http\Controllers\Communications;

use Illuminate\Http\Request;
use App\Models\Communications\NewsletterSubscription;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class NewsletterController extends Controller
{
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
