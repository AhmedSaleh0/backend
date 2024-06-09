<?php
namespace App\Http\Controllers\User;

use App\Models\User\UserSkill;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserSkillController extends Controller
{
    public function index()
    {
        // Get skills for the authenticated user
        $userSkills = UserSkill::where('user_id', Auth::id())->get();
        return response()->json($userSkills);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'skill_id' => 'required|exists:skills,id',
        ]);

        // Add the authenticated user's ID to the request data
        $validated['user_id'] = Auth::id();

        $userSkill = UserSkill::create($validated);
        return response()->json($userSkill, 201);
    }

    public function show(UserSkill $userSkill)
    {
        // Ensure the skill belongs to the authenticated user
        if ($userSkill->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($userSkill);
    }

    public function update(Request $request, UserSkill $userSkill)
    {
        // Ensure the skill belongs to the authenticated user
        if ($userSkill->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'skill_id' => 'required|exists:skills,id',
        ]);

        $userSkill->update($validated);
        return response()->json($userSkill);
    }

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
