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
     * @group iCan Reactions
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
     * @group iCan Reactions
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

        $userId = Auth::id();

        // Check if the user has already reacted to this post
        $existingReaction = ICanReaction::where('ican_id', $ican_id)->where('user_id', $userId)->first();

        if ($existingReaction) {
            return response()->json([
                'message' => 'You have already reacted to this post.'
            ], 400);
        }

        $reaction = ICanReaction::create([
            'ican_id' => $ican_id,
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
     * @group iCan Reactions
     * @urlParam ican_id int required The ID of the ICan post. Example: 1
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($ican_id)
    {
        $userId = Auth::id();
        $reaction = ICanReaction::where('ican_id', $ican_id)->where('user_id', $userId)->firstOrFail();
        $reaction->delete();

        return response()->json(['message' => 'Reaction deleted successfully']);
    }

/**
     * Display a listing of ICan posts liked by the current user.
     *
     * @group iCan Reactions
     * @return \Illuminate\Http\JsonResponse
     */
    public function myLikedIcan()
    {
        $userId = Auth::id();
        $likedIcans = ICan::whereHas('reactions', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->with(['user', 'user.image'])->get()->map(function ($post) {
            $post->liked_by_user = Auth::check() ? $post->isLikedByUser() : false;
            return $post;
        });

        return response()->json($likedIcans);
    }
}
