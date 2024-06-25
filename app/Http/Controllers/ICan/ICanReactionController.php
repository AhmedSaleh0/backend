<?php

namespace App\Http\Controllers\ICan;

use App\Models\ICan\ICanReaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\ICan\ICan;

class ICanReactionController extends Controller
{
    /**
     * Display a listing of reactions for a specific ICan post.
     *
     * @group ICan Reactions
     * @param int $ican_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($ican_id)
    {
        $reactions = ICanReaction::with(['user', 'user.image'])->where('ican_id', $ican_id)->get();
        return response()->json($reactions);
    }

    /**
     * Store a newly created reaction in storage.
     *
     * @group ICan Reactions
     * @param Request $request
     * @param int $ican_id
     * @bodyParam reaction_type string required The type of the reaction. Example: like
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $ican_id)
    {
        $request->validate([
            'reaction_type' => 'required|string',
        ]);

        $reaction = ICanReaction::create([
            'ican_id' => $ican_id,
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
     * @group ICan Reactions
     * @urlParam id int required The ID of the reaction. Example: 1
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $reaction = ICanReaction::with(['user', 'user.image'])->findOrFail($id);
        return response()->json($reaction);
    }

    /**
     * Update the specified reaction in storage.
     *
     * @group ICan Reactions
     * @param Request $request
     * @param int $id
     * @bodyParam reaction_type string required The type of the reaction. Example: like
     * @urlParam id int required The ID of the reaction. Example: 1
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'reaction_type' => 'required|string',
        ]);

        $reaction = ICanReaction::findOrFail($id);
        $reaction->update($request->all());

        return response()->json([
            'message' => 'Reaction updated successfully',
            'reaction' => $reaction
        ]);
    }

    /**
     * Remove the specified reaction from storage.
     *
     * @group ICan Reactions
     * @urlParam id int required The ID of the reaction. Example: 1
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $reaction = ICanReaction::findOrFail($id);
        $reaction->delete();

        return response()->json(['message' => 'Reaction deleted successfully']);
    }

    /**
     * Display a listing of ICan posts liked by the current user.
     *
     * @group ICan Reactions
     * @return \Illuminate\Http\JsonResponse
     */
    public function myLikedIcan()
    {
        $userId = Auth::id();
        $likedIcans = ICan::whereHas('reactions', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->with(['user', 'user.image'])->get();

        return response()->json($likedIcans);
    }
}
