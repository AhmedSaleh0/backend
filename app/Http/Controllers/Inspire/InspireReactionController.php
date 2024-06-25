<?php

namespace App\Http\Controllers\Inspire;

use App\Models\Inspire\InspireReaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Inspire\Inspire;

class InspireReactionController extends Controller
{
    /**
     * Display a listing of reactions for a specific Inspire post.
     *
     * @group Inspire Reactions
     * @param int $inspire_id
     * @return \Illuminate\Http\Response
     */
    public function index($inspire_id)
    {
        $reactions = InspireReaction::with(['user', 'user.image'])->where('inspire_id', $inspire_id)->get();
        return response()->json($reactions);
    }

    /**
     * Store a newly created reaction in storage.
     *
     * @group Inspire Reactions
     * @param Request $request
     * @param int $inspire_id
     * @bodyParam reaction_type integer required The type of the reaction. Example: 1
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $inspire_id)
    {
        $request->validate([
            'reaction_type' => 'required|integer',
        ]);

        $reaction = InspireReaction::create([
            'inspire_id' => $inspire_id,
            'user_id' => Auth::id(),
            'reaction_type' => $request->reaction_type,
        ]);

        return response()->json([
            'message' => 'Reaction added successfully',
            'reaction' => $reaction
        ], 201);
    }

    /**
     * Display the specified reaction.
     *
     * @group Inspire Reactions
     * @urlParam reaction_id int required The ID of the reaction. Example: 1
     * @return \Illuminate\Http\Response
     */
    public function show($reaction_id)
    {
        $reaction = InspireReaction::with(['user', 'user.image'])->findOrFail($reaction_id);
        return response()->json($reaction);
    }

    /**
     * Update the specified reaction in storage.
     *
     * @group Inspire Reactions
     * @param Request $request
     * @param int $reaction_id
     * @bodyParam reaction_type integer required The type of the reaction. Example: 1
     * @urlParam reaction_id int required The ID of the reaction. Example: 1
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $reaction_id)
    {
        $request->validate([
            'reaction_type' => 'required|integer',
        ]);

        $reaction = InspireReaction::findOrFail($reaction_id);
        $reaction->update($request->all());

        return response()->json([
            'message' => 'Reaction updated successfully',
            'reaction' => $reaction
        ]);
    }

    /**
     * Remove the specified reaction from storage.
     *
     * @group Inspire Reactions
     * @urlParam reaction_id int required The ID of the reaction. Example: 1
     * @return \Illuminate\Http\Response
     */
    public function destroy($reaction_id)
    {
        $reaction = InspireReaction::findOrFail($reaction_id);
        $reaction->delete();

        return response()->json(['message' => 'Reaction deleted successfully']);
    }

    /**
     * Display a listing of Inspire posts liked by the current user.
     *
     * @group Inspire Reactions
     * @response 200 {
     *   "id": 1,
     *   "type": "image",
     *   "title": "My New Post",
     *   "content": "This is the content of my post.",
     *   "media_url": "https://your-bucket.s3.your-region.amazonaws.com/inspire/1/media.jpg",
     *   "user_id": 1,
     *   "status": "active",
     *   "views": 100,
     *   "category": 1,
     *   "sub_category": 2,
     *   "liked_by_user": true,
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
    public function myLikedInspire()
    {
        $userId = Auth::id();
        $likedInspire = Inspire::whereHas('reactions', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->with(['user', 'user.image'])->get();

        return response()->json($likedInspire);
    }
}
