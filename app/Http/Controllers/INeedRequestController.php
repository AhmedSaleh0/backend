<?php

namespace App\Http\Controllers;

use App\Models\INeedRequest;
use App\Models\INeed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class INeedRequestController extends Controller
{
    /**
     * Display a listing of INeed requests.
     *
     * @group INeed Requests
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $requests = INeedRequest::all();
        return response()->json($requests);
    }

    /**
     * Users apply for an INeed request.
     *
     * @group INeed Requests
     * @bodyParam ineed_id int required The ID of the INeed post. Example: 1
     * @response 201 {
     *   "message": "Request created successfully",
     *   "request": {
     *     "id": 1,
     *     "ineed_id": 1,
     *     "user_id": 1,
     *     "status": "pending",
     *     "created_at": "2024-06-05T12:00:00.000000Z",
     *     "updated_at": "2024-06-05T12:00:00.000000Z"
     *   }
     * }
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apply(Request $request)
    {
        $request->validate([
            'ineed_id' => 'required|exists:i_need,id',
        ]);

        $ineedRequest = INeedRequest::create([
            'ineed_id' => $request->ineed_id,
            'user_id' => Auth::id(),
            'status' => 'pending',
        ]);

        return response()->json(['message' => 'Request created successfully', 'request' => $ineedRequest], 201);
    }

    /**
     * Owner accepts an INeed request.
     *
     * @group INeed Requests
     * @urlParam id int required The ID of the request. Example: 1
     * @response 200 {
     *   "message": "Request accepted successfully",
     *   "request": {
     *     "id": 1,
     *     "ineed_id": 1,
     *     "user_id": 1,
     *     "status": "accepted",
     *     "created_at": "2024-06-05T12:00:00.000000Z",
     *     "updated_at": "2024-06-05T12:00:00.000000Z"
     *   }
     * }
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function accept($id)
    {
        $ineedRequest = INeedRequest::findOrFail($id);
        $ineedPost = INeed::findOrFail($ineedRequest->ineed_id);

        if (Auth::id() !== $ineedPost->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $ineedRequest->update(['status' => 'accepted']);

        return response()->json(['message' => 'Request accepted successfully', 'request' => $ineedRequest]);
    }

    /**
     * Owner rejects an INeed request.
     *
     * @group INeed Requests
     * @urlParam id int required The ID of the request. Example: 1
     * @response 200 {
     *   "message": "Request rejected successfully",
     *   "request": {
     *     "id": 1,
     *     "ineed_id": 1,
     *     "user_id": 1,
     *     "status": "rejected",
     *     "created_at": "2024-06-05T12:00:00.000000Z",
     *     "updated_at": "2024-06-05T12:00:00.000000Z"
     *   }
     * }
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function reject($id)
    {
        $ineedRequest = INeedRequest::findOrFail($id);
        $ineedPost = INeed::findOrFail($ineedRequest->ineed_id);

        if (Auth::id() !== $ineedPost->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $ineedRequest->update(['status' => 'rejected']);

        return response()->json(['message' => 'Request rejected successfully', 'request' => $ineedRequest]);
    }

    /**
     * Display the specified INeed request.
     *
     * @group INeed Requests
     * @urlParam id int required The ID of the request. Example: 1
     * @response 200 {
     *   "id": 1,
     *   "ineed_id": 1,
     *   "user_id": 1,
     *   "status": "pending",
     *   "created_at": "2024-06-05T12:00:00.000000Z",
     *   "updated_at": "2024-06-05T12:00:00.000000Z"
     * }
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $ineedRequest = INeedRequest::findOrFail($id);
        return response()->json($ineedRequest);
    }

    /**
     * Remove the specified INeed request from storage.
     *
     * @group INeed Requests
     * @urlParam id int required The ID of the request. Example: 1
     * @response 200 {
     *   "message": "Request deleted successfully"
     * }
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $ineedRequest = INeedRequest::findOrFail($id);
        $ineedRequest->delete();

        return response()->json(['message' => 'Request deleted successfully']);
    }
}
