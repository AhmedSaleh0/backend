<?php

namespace App\Http\Controllers\ICan;

use App\Models\ICan\ICanRequest;
use App\Models\ICan\ICan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class ICanRequestController extends Controller
{
    /**
     * Display a listing of ICan requests.
     *
     * @group ICan Requests
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $requests = ICanRequest::all();
        return response()->json($requests);
    }

    /**
     * Users apply for an ICan request.
     *
     * @group ICan Requests
     * @bodyParam ican_id int required The ID of the ICan post. Example: 1
     * @response 201 {
     *   "message": "Request created successfully",
     *   "request": {
     *     "id": 1,
     *     "ican_id": 1,
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
            'ican_id' => 'required|exists:i_can,id',
        ]);

        $icanRequest = ICanRequest::create([
            'ican_id' => $request->ican_id,
            'user_id' => Auth::id(),
            'status' => 'pending',
        ]);

        return response()->json(['message' => 'Request created successfully', 'request' => $icanRequest], 201);
    }

    /**
     * Owner accepts an ICan request.
     *
     * @group ICan Requests
     * @urlParam request_id int required The ID of the request. Example: 1
     * @response 200 {
     *   "message": "Request accepted successfully",
     *   "request": {
     *     "id": 1,
     *     "ican_id": 1,
     *     "user_id": 1,
     *     "status": "accepted",
     *     "created_at": "2024-06-05T12:00:00.000000Z",
     *     "updated_at": "2024-06-05T12:00:00.000000Z"
     *   }
     * }
     * @param int $request_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function accept($request_id)
    {
        $icanRequest = ICanRequest::findOrFail($request_id);
        $icanPost = ICan::findOrFail($icanRequest->ican_id);

        if (Auth::id() !== $icanPost->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $icanRequest->update(['status' => 'accepted']);

        return response()->json(['message' => 'Request accepted successfully', 'request' => $icanRequest]);
    }

    /**
     * Owner rejects an ICan request.
     *
     * @group ICan Requests
     * @urlParam request_id int required The ID of the request. Example: 1
     * @response 200 {
     *   "message": "Request rejected successfully",
     *   "request": {
     *     "id": 1,
     *     "ican_id": 1,
     *     "user_id": 1,
     *     "status": "rejected",
     *     "created_at": "2024-06-05T12:00:00.000000Z",
     *     "updated_at": "2024-06-05T12:00:00.000000Z"
     *   }
     * }
     * @param int $request_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function reject($request_id)
    {
        $icanRequest = ICanRequest::findOrFail($request_id);
        $icanPost = ICan::findOrFail($icanRequest->ican_id);

        if (Auth::id() !== $icanPost->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $icanRequest->update(['status' => 'rejected']);

        return response()->json(['message' => 'Request rejected successfully', 'request' => $icanRequest]);
    }

    /**
     * Display the specified ICan request.
     *
     * @group ICan Requests
     * @urlParam request_id int required The ID of the request. Example: 1
     * @response 200 {
     *   "id": 1,
     *   "ican_id": 1,
     *   "user_id": 1,
     *   "status": "pending",
     *   "created_at": "2024-06-05T12:00:00.000000Z",
     *   "updated_at": "2024-06-05T12:00:00.000000Z"
     * }
     * @param int $request_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($request_id)
    {
        $icanRequest = ICanRequest::findOrFail($request_id);
        return response()->json($icanRequest);
    }

    /**
     * Remove the specified ICan request from storage.
     *
     * @group ICan Requests
     * @urlParam request_id int required The ID of the request. Example: 1
     * @response 200 {
     *   "message": "Request deleted successfully"
     * }
     * @param int $request_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($request_id)
    {
        $icanRequest = ICanRequest::findOrFail($request_id);
        $icanRequest->delete();

        return response()->json(['message' => 'Request deleted successfully']);
    }
}
