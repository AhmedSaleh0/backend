<?php

namespace App\Http\Controllers;

use App\Models\InspireReaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
     * @urlParam id int required The ID of the reaction. Example: 1
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $reaction = InspireReaction::with(['user', 'user.image'])->findOrFail($id);
        return response()->json($reaction);
    }

    /**
     * Update the specified reaction in storage.
     *
     * @group Inspire Reactions
     * @param Request $request
     * @param int $id
     * @bodyParam reaction_type integer required The type of the reaction. Example: 1
     * @urlParam id int required The ID of the reaction. Example: 1
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'reaction_type' => 'required|integer',
        ]);

        $reaction = InspireReaction::findOrFail($id);
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
     * @urlParam id int required The ID of the reaction. Example: 1
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $reaction = InspireReaction::findOrFail($id);
        $reaction->delete();

        return response()->json(['message' => 'Reaction deleted successfully']);
    }
}
