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
     * @param int $categoryId
     * @return \Illuminate\Http\JsonResponse
     */
    public function skillsByCategory($categoryId)
    {
        $skills = Skill::where('category_id', $categoryId)->get();
        return response()->json($skills);
    }
}
