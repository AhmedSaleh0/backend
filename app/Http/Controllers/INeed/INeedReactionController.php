<?php

namespace App\Http\Controllers\INeed;

use App\Models\INeed\INeedReaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\INeed\INeed;

class INeedReactionController extends Controller
{
    /**
     * Display a listing of reactions for a specific I-Need post.
     *
     * @group I-Need Reactions
     * @urlParam ineed_id required The ID of the I-Need post. Example: 1
     * @response 200 {
     *   "id": 1,
     *   "ineed_id": 1,
     *   "user_id": 1,
     *   "reaction_type": 1,
     *   "created_at": "2024-06-09T00:00:00.000000Z",
     *   "updated_at": "2024-06-09T00:00:00.000000Z",
     *   "user": {
     *     "id": 1,
     *     "first_name": "John",
     *     "last_name": "Doe",
     *     "email": "john.doe@example.com",
     *     "created_at": "2024-06-09T00:00:00.000000Z",
     *     "updated_at": "2024-06-09T00:00:00.000000Z"
     *   }
     * }
     */
    public function index($ineed_id)
    {
        $reactions = INeedReaction::with(['user', 'user.image'])->where('ineed_id', $ineed_id)->get();
        return response()->json($reactions);
    }

    /**
     * Store a newly created reaction in storage.
     *
     * @group I-Need Reactions
     * @urlParam ineed_id required The ID of the I-Need post. Example: 1
     * @bodyParam reaction_type integer required The type of the reaction. Example: 1
     * @response 201 {
     *   "message": "Reaction added successfully",
     *   "reaction": {
     *     "id": 1,
     *     "ineed_id": 1,
     *     "user_id": 1,
     *     "reaction_type": 1,
     *     "created_at": "2024-06-09T00:00:00.000000Z",
     *     "updated_at": "2024-06-09T00:00:00.000000Z"
     *   }
     * }
     */
    public function store(Request $request, $ineed_id)
    {
        $request->validate([
            'reaction_type' => 'required|integer',
        ]);

        $userId = Auth::id();

        // Check if the user has already reacted to this post
        $existingReaction = INeedReaction::where('ineed_id', $ineed_id)->where('user_id', $userId)->first();

        if ($existingReaction) {
            return response()->json([
                'message' => 'You have already reacted to this post.'
            ], 400);
        }

        $reaction = INeedReaction::create([
            'ineed_id' => $ineed_id,
            'user_id' => $userId,
            'reaction_type' => $request->reaction_type,
        ]);

        return response()->json([
            'message' => 'Reaction added successfully',
            'reaction' => $reaction
        ], 201);
    }

    /**
     * Remove the specified reaction from storage.
     *
     * @group I-Need Reactions
     * @urlParam ineed_id required The ID of the I-Need post. Example: 1
     * @response 200 {
     *   "message": "Reaction deleted successfully"
     * }
     */
    public function destroy($ineed_id)
    {
        $userId = Auth::id();
        $reaction = INeedReaction::where('ineed_id', $ineed_id)->where('user_id', $userId)->firstOrFail();
        $reaction->delete();

        return response()->json(['message' => 'Reaction deleted successfully']);
    }

    /**
     * Display a listing of I-Need posts liked by the current user.
     *
     * @group I-Need Reactions
     * @response 200 {
     *   "id": 1,
     *   "type": "image",
     *   "title": "My New Request",
     *   "short_description": "This is a short description of my request.",
     *   "image": "https://your-bucket.s3.your-region.amazonaws.com/ineed/1/image.jpg",
     *   "price": 99.99,
     *   "price_type": "fixed",
     *   "status": "pending",
     *   "location": "New York",
     *   "experience": "Entry",
     *   "skills": [1, 2, 3],
     *   "created_at": "2024-06-05T12:00:00.000000Z",
     *   "updated_at": "2024-06-05T12:00:00.000000Z",
     *   "user": {
     *     "id": 1,
     *     "first_name": "John",
     *     "last_name": "Doe",
     *     "email": "john.doe@example.com",
     *     "created_at": "2024-06-09T00:00:00.000000Z",
     *     "updated_at": "2024-06-09T00:00:00.000000Z",
     *     "image": {
     *       "id": 1,
     *       "url": "https://your-bucket.s3.your-region.amazonaws.com/user_images/1/image.jpg"
     *     }
     *   }
     * }
     */
    public function myLikedINeed()
    {
        $userId = Auth::id();
        $likedINeed = INeed::whereHas('reactions', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->with(['user', 'user.image'])->get();

        return response()->json($likedINeed);
    }
}
