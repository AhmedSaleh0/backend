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
     * @group Skills
     * @bodyParam limit int Optional The number of skills to return. If 0, return all skills. Default is 10. Example: 5
     * @bodyParam random bool Optional Whether to return the skills in random order. Default is false. Example: true
     * 
     * @example GET /skills
     * @example GET /skills?limit=5
     * @example GET /skills?limit=5&random=true
     * @example GET /skills?limit=0
     * @example GET /skills?limit=0&random=true
     *
     * @response 200 {
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

    /**
     * Store a newly created skill in storage.
     *
     * @group Skills
     * @bodyParam name string required The name of the skill. Example: "Programming"
     * @bodyParam category string required The category of the skill. Example: "Technical"
     * @bodyParam sub_category string Optional The sub-category of the skill. Example: "Software Development"
     * 
     * @response 201 {
     *  "id": 1,
     *  "name": "Programming",
     *  "category": "Technical",
     *  "sub_category": "Software Development",
     *  "created_at": "2024-05-28T00:00:00.000000Z",
     *  "updated_at": "2024-05-28T00:00:00.000000Z"
     * }
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Display the specified skill.
     *
     * @group Skills
     * @urlParam id int required The ID of the skill. Example: 1
     * 
     * @response 200 {
     *  "id": 1,
     *  "name": "Programming",
     *  "category": "Technical",
     *  "sub_category": "Software Development",
     *  "created_at": "2024-05-28T00:00:00.000000Z",
     *  "updated_at": "2024-05-28T00:00:00.000000Z"
     * }
     *
     * @param Skill $skill
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Skill $skill)
    {
        return response()->json($skill);
    }

    /**
     * Update the specified skill in storage.
     *
     * @group Skills
     * @urlParam id int required The ID of the skill. Example: 1
     * 
     * @bodyParam name string required The name of the skill. Example: "Programming"
     * @bodyParam category string required The category of the skill. Example: "Technical"
     * @bodyParam sub_category string Optional The sub-category of the skill. Example: "Software Development"
     * 
     * @response 200 {
     *  "id": 1,
     *  "name": "Programming",
     *  "category": "Technical",
     *  "sub_category": "Software Development",
     *  "created_at": "2024-05-28T00:00:00.000000Z",
     *  "updated_at": "2024-05-28T00:00:00.000000Z"
     * }
     *
     * @param Request $request
     * @param Skill $skill
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Remove the specified skill from storage.
     *
     * @group Skills
     * @urlParam id int required The ID of the skill. Example: 1
     * 
     * @response 200 {
     *  "message": "Skill deleted successfully"
     * }
     *
     * @param Skill $skill
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Skill $skill)
    {
        $skill->delete();
        return response()->json(['message' => 'Skill deleted successfully']);
    }
}
