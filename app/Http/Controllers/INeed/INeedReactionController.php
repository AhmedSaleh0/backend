<?php

namespace App\Http\Controllers\INeed;

use App\Models\INeed\INeedReaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

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

        $reaction = INeedReaction::create([
            'ineed_id' => $ineed_id,
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
     * @group I-Need Reactions
     * @urlParam id required The ID of the reaction. Example: 1
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
    public function show($id)
    {
        $reaction = INeedReaction::with(['user', 'user.image'])->findOrFail($id);
        return response()->json($reaction);
    }

    /**
     * Update the specified reaction in storage.
     *
     * @group I-Need Reactions
     * @urlParam id required The ID of the reaction. Example: 1
     * @bodyParam reaction_type integer required The type of the reaction. Example: 1
     * @response 200 {
     *   "message": "Reaction updated successfully",
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
    public function update(Request $request, $id)
    {
        $request->validate([
            'reaction_type' => 'required|integer',
        ]);

        $reaction = INeedReaction::findOrFail($id);
        $reaction->update($request->all());

        return response()->json([
            'message' => 'Reaction updated successfully',
            'reaction' => $reaction
        ]);
    }

    /**
     * Remove the specified reaction from storage.
     *
     * @group I-Need Reactions
     * @urlParam id required The ID of the reaction. Example: 1
     * @response 200 {
     *   "message": "Reaction deleted successfully"
     * }
     */
    public function destroy($id)
    {
        $reaction = INeedReaction::findOrFail($id);
        $reaction->delete();

        return response()->json(['message' => 'Reaction deleted successfully']);
    }
}
