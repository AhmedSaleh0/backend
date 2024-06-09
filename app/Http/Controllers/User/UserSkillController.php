<?php
namespace App\Http\Controllers\User;

use App\Models\User\UserSkill;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserSkillController extends Controller
{
    public function index()
    {
        $userSkills = UserSkill::all();
        return response()->json($userSkills);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'skill_id' => 'required|exists:skills,id',
        ]);

        $userSkill = UserSkill::create($validated);
        return response()->json($userSkill, 201);
    }

    public function show(UserSkill $userSkill)
    {
        return response()->json($userSkill);
    }

    public function update(Request $request, UserSkill $userSkill)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'skill_id' => 'required|exists:skills,id',
        ]);

        $userSkill->update($validated);
        return response()->json($userSkill);
    }

    public function destroy(UserSkill $userSkill)
    {
        $userSkill->delete();
        return response()->json(['message' => 'User skill deleted successfully']);
    }
}
