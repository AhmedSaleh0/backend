<?php

namespace App\Http\Controllers\Skill;

use App\Models\Skill\Skill;
use App\Models\Skill\SkillsSubCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SkillQueryController extends Controller
{
    /**
     * Display a listing of sub-categories for the selected category.
     *
     * @group Skills
     * @urlParam categoryId int required The ID of the category. Example: 1
     * 
     * @response 200 {
     *  "id": 1,
     *  "name": "Sub-category Name",
     *  "category_id": 1,
     *  "created_at": "2024-05-28T00:00:00.000000Z",
     *  "updated_at": "2024-05-28T00:00:00.000000Z"
     * }
     *
     * @param int $categoryId
     * @return \Illuminate\Http\JsonResponse
     */
    public function subCategories($categoryId)
    {
        $subCategories = SkillsSubCategory::where('category_id', $categoryId)->get();
        return response()->json($subCategories);
    }

    /**
     * Display a listing of skills for the specific sub-category.
     *
     * @group Skills
     * @urlParam subCategoryId int required The ID of the sub-category. Example: 1
     * 
     * @response 200 {
     *  "id": 1,
     *  "name": "Skill Name",
     *  "category_id": 1,
     *  "sub_category_id": 1,
     *  "created_at": "2024-05-28T00:00:00.000000Z",
     *  "updated_at": "2024-05-28T00:00:00.000000Z"
     * }
     *
     * @param int $subCategoryId
     * @return \Illuminate\Http\JsonResponse
     */
    public function skillsBySubCategory($subCategoryId)
    {
        $skills = Skill::where('sub_category_id', $subCategoryId)->get();
        return response()->json($skills);
    }

    /**
     * Display a listing of skills for the specific category.
     *
     * @group Skills
     * @urlParam categoryId int required The ID of the category. Example: 1
     * 
     * @response 200 {
     *  "id": 1,
     *  "name": "Skill Name",
     *  "category_id": 1,
     *  "sub_category_id": 1,
     *  "created_at": "2024-05-28T00:00:00.000000Z",
     *  "updated_at": "2024-05-28T00:00:00.000000Z"
     * }
     *
     * @param int $categoryId
     * @return \Illuminate\Http\JsonResponse
     */
    public function skillsByCategory($categoryId)
    {
        $skills = Skill::where('category_id', $categoryId)->get();
        return response()->json($skills);
    }
}
