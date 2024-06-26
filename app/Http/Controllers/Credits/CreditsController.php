<?php

namespace App\Http\Controllers\Credits;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CreditsController extends Controller
{
    /**
     * Get the user's credit balance.
     *
     * @group Credits
     * @response 200 {
     *  "balance": 100
     * }
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function balance()
    {
        // Return user's credit balance
        $balance = 100; // Replace with actual balance retrieval logic
        return response()->json(['balance' => $balance], 200);
    }

    /**
     * Add credits to the user's account.
     *
     * @group Credits
     * @bodyParam amount int required The amount of credits to add. Example: 50
     * @response 200 {
     *  "message": "Credits added successfully",
     *  "balance": 150
     * }
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addCredits(Request $request)
    {
        // Validate the request
        $request->validate(['amount' => 'required|integer']);

        // Add credits logic
        $balance = 150; // Replace with actual logic to add credits

        return response()->json(['message' => 'Credits added successfully', 'balance' => $balance], 200);
    }

    /**
     * Deduct credits from the user's account.
     *
     * @group Credits
     * @bodyParam amount int required The amount of credits to deduct. Example: 30
     * @response 200 {
     *  "message": "Credits deducted successfully",
     *  "balance": 70
     * }
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deductCredits(Request $request)
    {
        // Validate the request
        $request->validate(['amount' => 'required|integer']);

        // Deduct credits logic
        $balance = 70; // Replace with actual logic to deduct credits

        return response()->json(['message' => 'Credits deducted successfully', 'balance' => $balance], 200);
    }

    /**
     * Handle credit purchase through payment gateway.
     *
     * @group Credits
     * @bodyParam amount int required The amount of credits to purchase. Example: 100
     * @response 200 {
     *  "message": "Credits purchased successfully",
     *  "balance": 200
     * }
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function purchaseCredits(Request $request)
    {
        // Validate the request
        $request->validate(['amount' => 'required|integer']);

        // Purchase credits logic
        $balance = 200; // Replace with actual logic to handle credit purchase

        return response()->json(['message' => 'Credits purchased successfully', 'balance' => $balance], 200);
    }
}
