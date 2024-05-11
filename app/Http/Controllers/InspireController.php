<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inspire;

/**
 * Class InspireController
 *
 * @package App\Http\Controllers
 */
class InspireController extends Controller
{
    /**
     * Display a listing of the video posts.
     *
     * @OA\Get(
     *     path="/api/inspire/posts",
     *     tags={"Inspire"},
     *     summary="List all video posts",
     *     operationId="indexInspire",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/InspirePost")
     *         )
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function index()
    {
        // Fetch all the inspire posts from database
    }

    /**
     * Store a newly created video post in storage.
     *
     * @OA\Post(
     *     path="/api/inspire/posts",
     *     tags={"Inspire"},
     *     summary="Create a new video post",
     *     operationId="storeInspire",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Pass video post data",
     *         @OA\JsonContent(
     *             required={"video_url", "description"},
     *             @OA\Property(property="video_url", type="string", format="url", example="http://example.com/video.mp4"),
     *             @OA\Property(property="description", type="string", example="A brief description of the video")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Video post created"
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function store(Request $request)
    {
        // Validate and create a new inspire post
    }

    /**
     * Display the specified video post by ID.
     *
     * @OA\Get(
     *     path="/api/inspire/posts/{id}",
     *     tags={"Inspire"},
     *     summary="Get video post by ID",
     *     operationId="showInspire",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the video post",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/InspirePost")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found"
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function show($id)
    {
        // Retrieve a single inspire post by id
    }

    /**
     * Update the specified video post in storage.
     *
     * @OA\Put(
     *     path="/api/inspire/posts/{id}",
     *     tags={"Inspire"},
     *     summary="Update a video post",
     *     operationId="updateInspire",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the video post to update",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Pass video post updated data",
     *         @OA\JsonContent(
     *             @OA\Property(property="video_url", type="string", format="url"),
     *             @OA\Property(property="description", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Video post updated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found"
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function update(Request $request, $id)
    {
        // Validate and update the inspire post
    }

    /**
     * Remove the specified video post from storage.
     *
     * @OA\Delete(
     *     path="/api/inspire/posts/{id}",
     *     tags={"Inspire"},
     *     summary="Delete a video post",
     *     operationId="deleteInspire",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the video post to delete",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Video post deleted"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found"
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function destroy($id)
    {
        // Delete the inspire post
    }
}
