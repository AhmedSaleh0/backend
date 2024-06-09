<?php

namespace App\Http\Controllers\User;

use App\Models\User\UserSkill;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserSkillController extends Controller
{
    /**
     * Display a listing of the user's skills.
     *
     * @authenticated
     *
     * @response 200 {
     *   "id": 1,
     *   "user_id": 1,
     *   "skill_id": 1,
     *   "created_at": "2024-06-09T18:35:23.000000Z",
     *   "updated_at": "2024-06-09T18:35:23.000000Z"
     * }
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Get skills for the authenticated user
        $userSkills = UserSkill::where('user_id', Auth::id())->get();
        return response()->json($userSkills);
    }

    /**
     * Store a newly created user skill in storage.
     *
     * @authenticated
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 
     * @bodyParam skill_id int required The ID of the skill. Example: 1
     * 
     * @response 201 {
     *   "id": 1,
     *   "user_id": 1,
     *   "skill_id": 1,
     *   "created_at": "2024-06-09T18:35:23.000000Z",
     *   "updated_at": "2024-06-09T18:35:23.000000Z"
     * }
     * @response 409 {
     *   "message": "User already has this skill"
     * }
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'skill_id' => 'required|exists:skills,id',
        ]);

        // Check if the user already has this skill
        $existingSkill = UserSkill::where('user_id', Auth::id())
                                   ->where('skill_id', $request->skill_id)
                                   ->first();

        if ($existingSkill) {
            return response()->json(['message' => 'User already has this skill'], 409);
        }

        // Add the authenticated user's ID to the request data
        $validated['user_id'] = Auth::id();

        $userSkill = UserSkill::create($validated);
        return response()->json($userSkill, 201);
    }

    /**
     * Remove the specified user skill from storage.
     *
     * @authenticated
     *
     * @param UserSkill $userSkill
     * @return \Illuminate\Http\JsonResponse
     * 
     * @response 200 {
     *   "message": "User skill deleted successfully"
     * }
     * @response 403 {
     *   "message": "Unauthorized"
     * }
     */
    public function destroy(UserSkill $userSkill)
    {
        // Ensure the skill belongs to the authenticated user
        if ($userSkill->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $userSkill->delete();
        return response()->json(['message' => 'User skill deleted successfully']);
    }
}
