<?php

namespace App\Http\Controllers\Common;

use Illuminate\Http\Request;
use App\Models\Common\Rating;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class RatingController extends Controller
{
    /**
     * Store a new rating and review
     * 
     * @group Ratings
     * @bodyParam rated_id int required The ID of the user being rated. Example: 1
     * @bodyParam rateable_id int required The ID of the entity being rated. Example: 1
     * @bodyParam rateable_type string required The type of the entity being rated. Example: iNeed, iCan
     * @bodyParam rating int required The rating value between 1 and 5. Example: 5
     * @bodyParam review string The review content. Example: Great job!
     * @response 201 {
     *   "id": 1,
     *   "user_id": 1,
     *   "rateable_id": 2,
     *   "rateable_type": "iNeed",
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
            'rateable_id' => 'required|integer',
            'rateable_type' => 'required|in:iNeed,iCan',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string',
        ]);

        $rating = Rating::create([
            'user_id' => Auth::id(),
            'rated_id' => $validatedData['rated_id'],
            'rateable_id' => $validatedData['rateable_id'],
            'rateable_type' => $validatedData['rateable_type'],
            'rating' => $validatedData['rating'],
            'review' => $validatedData['review'],
            'status' => 'Pending',
        ]);

        return response()->json($rating, 201);
    }

    /**
     * Get a list of ratings for iNeed
     * 
     * @group Ratings
     * @response 200 {
     *   "id": 1,
     *   "user_id": 1,
     *   "rateable_id": 2,
     *   "rateable_type": "iNeed",
     *   "rating": 5,
     *   "review": "Great job!",
     *   "status": "Approved",
     *   "created_at": "2024-07-06T00:00:00.000000Z",
     *   "updated_at": "2024-07-06T00:00:00.000000Z"
     * }
     */
    public function indexINeed(Request $request)
    {
        $validatedData = $request->validate([
            'rateable_id' => 'required|integer',
        ]);

        $ratings = Rating::where('rateable_type', 'iNeed')
            ->where('rateable_id', $validatedData['rateable_id'])
            ->where('status', 'Approved')
            ->get();

        return response()->json($ratings);
    }

    /**
     * Get a list of ratings for iCan
     * 
     * @group Ratings
     * @response 200 {
     *   "id": 1,
     *   "user_id": 1,
     *   "rateable_id": 2,
     *   "rateable_type": "iCan",
     *   "rating": 5,
     *   "review": "Great job!",
     *   "status": "Approved",
     *   "created_at": "2024-07-06T00:00:00.000000Z",
     *   "updated_at": "2024-07-06T00:00:00.000000Z"
     * }
     */
    public function indexICan(Request $request)
    {
        $validatedData = $request->validate([
            'rateable_id' => 'required|integer',
        ]);

        $ratings = Rating::where('rateable_type', 'iCan')
            ->where('rateable_id', $validatedData['rateable_id'])
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
     *   "rateable_id": 2,
     *   "rateable_type": "iNeed",
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
