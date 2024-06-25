<?php

namespace App\Http\Controllers\Skill;

use App\Models\Skill\Skill;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SkillController extends Controller
{
    /**
     * Display a list of skills.
     * 
     * @bodyParam limit int Optional The number of skills to return. If 0, return all skills. Default is 10. Example: 5
     * @bodyParam random bool Optional Whether to return the skills in random order. Default is false. Example: true
     *
     * @response {
     *  "id": 1,
     *  "name": "Skill Name",
     *  "category": 1,
     *  "sub_category": 1,
     *  "created_at": "2024-05-28T00:00:00.000000Z",
     *  "updated_at": "2024-05-28T00:00:00.000000Z"
     * }
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Get the limit and random parameters from the request, default to 10 and false if not provided
        $limit = $request->input('limit', 10);
        $random = $request->input('random', false);

        // If random is true, get the skills in random order, otherwise get them in order
        $query = $random ? Skill::inRandomOrder() : Skill::orderBy('id');

        // If limit is 0, return all skills, otherwise apply the limit
        $skills = $limit == 0 ? $query->get() : $query->limit($limit)->get();

        return response()->json($skills);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'sub_category' => 'nullable|string|max:255',
        ]);

        $skill = Skill::create($validatedData);
        return response()->json($skill, 201);
    }

    public function show(Skill $skill)
    {
        return response()->json($skill);
    }

    public function update(Request $request, Skill $skill)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'sub_category' => 'nullable|string|max:255',
        ]);

        $skill->update($validatedData);
        return response()->json($skill);
    }

    public function destroy(Skill $skill)
    {
        $skill->delete();
        return response()->json(['message' => 'Skill deleted successfully']);
    }
}
