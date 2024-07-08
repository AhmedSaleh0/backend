<?php

namespace App\Http\Controllers\Common;

use App\Models\Common\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class RatingController extends Controller
{
    /**
     * Store a new rating and review
     * 
     * @group Ratings
     * @bodyParam rated_id int required The ID of the user being rated. Example: 1
     * @bodyParam type string required The type of the rating. Example: iNeed, iCan
     * @bodyParam rating int required The rating value between 1 and 5. Example: 5
     * @bodyParam review string The review content. Example: Great job!
     * @response 201 {
     *   "id": 1,
     *   "user_id": 1,
     *   "rated_id": 2,
     *   "type": "iNeed",
     *   "rating": 5,
     *   "review": "Great job!",
     *   "status": "Pending",
     *   "created_at": "2024-07-06T00:00:00.000000Z",
     *   "updated_at": "2024-07-06T00:00:00.000000Z"
     * }
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'rated_id' => 'required|exists:users,id',
            'type' => 'required|in:iNeed,iCan',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string',
        ]);

        $rating = Rating::create([
            'user_id' => Auth::id(),
            'rated_id' => $validatedData['rated_id'],
            'type' => $validatedData['type'],
            'rating' => $validatedData['rating'],
            'review' => $validatedData['review'],
            'status' => 'Pending',
        ]);

        return response()->json($rating, 201);
    }

    /**
     * Get a list of ratings
     * 
     * @group Ratings
     * @queryParam type string The type of the rating to filter by. Example: iNeed
     * @response 200 {
     *   "id": 1,
     *   "user_id": 1,
     *   "rated_id": 2,
     *   "type": "iNeed",
     *   "rating": 5,
     *   "review": "Great job!",
     *   "status": "Approved",
     *   "created_at": "2024-07-06T00:00:00.000000Z",
     *   "updated_at": "2024-07-06T00:00:00.000000Z"
     * }
     */
    public function index(Request $request)
    {
        $ratings = Rating::where('type', $request->input('type', 'iNeed'))
            ->where('status', 'Approved')
            ->get();

        return response()->json($ratings);
    }

    /**
     * Update the status of a rating
     * 
     * @group Ratings
     * @urlParam rating int required The ID of the rating. Example: 1
     * @bodyParam status string required The new status of the rating. Example: Approved
     * @response 200 {
     *   "id": 1,
     *   "user_id": 1,
     *   "rated_id": 2,
     *   "type": "iNeed",
     *   "rating": 5,
     *   "review": "Great job!",
     *   "status": "Approved",
     *   "created_at": "2024-07-06T00:00:00.000000Z",
     *   "updated_at": "2024-07-06T00:00:00.000000Z"
     * }
     */
    public function updateStatus(Request $request, Rating $rating)
    {
        $validatedData = $request->validate([
            'status' => 'required|in:Pending,Approved,Rejected',
        ]);

        $rating->update(['status' => $validatedData['status']]);

        return response()->json($rating);
    }
}
