<?php

namespace App\Http\Controllers\ICan;

use App\Models\ICan\ICan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class ICanController extends Controller
{
    /**
     * Display a listing of ICan posts.
     *
     * @group ICan Posts
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $posts = ICan::all();
        return response()->json($posts);
    }

    /**
     * Store a newly created ICan post in storage.
     *
     * @group ICan Posts
     * @bodyParam title string required The title of the post. Example: My New Service
     * @bodyParam short_description string required A short description of the post. Example: This is a short description of my service.
     * @bodyParam image file nullable An image associated with the post (max 25MB).
     * @bodyParam price numeric required The price of the service. Example: 99.99
     * @bodyParam price_type string required The type of pricing (fixed or hourly). Example: fixed
     * @bodyParam status string required The status of the post (active or inactive). Example: active
     * @bodyParam location string nullable The location of the post. Example: Dubai
     * @bodyParam experience string nullable The experience required for the post. Example: 5 years
     * @response 201 {
     *   "message": "Post created successfully",
     *   "post": {
     *     "id": 1,
     *     "title": "My New Service",
     *     "short_description": "This is a short description of my service.",
     *     "image": "https://your-bucket.s3.your-region.amazonaws.com/ican/1/image.jpg",
     *     "price": 99.99,
     *     "price_type": "fixed",
     *     "status": "active",
     *     "location": "Dubai",
     *     "experience": "5 years",
     *     "created_at": "2024-06-05T12:00:00.000000Z",
     *     "updated_at": "2024-06-05T12:00:00.000000Z"
     *   }
     * }
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'required|string|max:255',
            'image' => 'nullable|image|max:25600', // 25MB max size
            'price' => 'required|numeric',
            'price_type' => 'required|in:fixed,hourly',
            'status' => 'required|in:active,inactive',
            'location' => 'nullable|string|max:255',
            'experience' => 'nullable|string|max:255'
        ]);

        $post = ICan::create($request->except('image'));

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store("ican/{$post->id}", 's3');
            $post->image = Storage::disk('s3')->url($path);
            $post->save();
        }

        return response()->json(['message' => 'Post created successfully', 'post' => $post], 201);
    }

    /**
     * Display the specified ICan post.
     *
     * @group ICan Posts
     * @urlParam ican_id int required The ID of the post. Example: 1
     * @response 200 {
     *   "id": 1,
     *   "title": "My New Service",
     *   "short_description": "This is a short description of my service.",
     *   "image": "https://your-bucket.s3.your-region.amazonaws.com/ican/1/image.jpg",
     *   "price": 99.99,
     *   "price_type": "fixed",
     *   "status": "active",
     *   "location": "Dubai",
     *   "experience": "5 years",
     *   "created_at": "2024-06-05T12:00:00.000000Z",
     *   "updated_at": "2024-06-05T12:00:00.000000Z"
     * }
     * @param int $ican_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($ican_id)
    {
        $post = ICan::findOrFail($ican_id);
        return response()->json($post);
    }

    /**
     * Update the specified ICan post in storage.
     *
     * @group ICan Posts
     * @urlParam ican_id int required The ID of the post. Example: 1
     * @bodyParam title string The title of the post. Example: My Updated Service
     * @bodyParam short_description string A short description of the post. Example: This is an updated short description of my service.
     * @bodyParam image file An image associated with the post (max 25MB).
     * @bodyParam price numeric The price of the service. Example: 99.99
     * @bodyParam price_type string The type of pricing (fixed or hourly). Example: fixed
     * @bodyParam status string The status of the post (active or inactive). Example: active
     * @bodyParam location string The location of the post. Example: Dubai
     * @bodyParam experience string The experience required for the post. Example: 5 years
     * @response 200 {
     *   "message": "Post updated successfully",
     *   "post": {
     *     "id": 1,
     *     "title": "My Updated Service",
     *     "short_description": "This is an updated short description of my service.",
     *     "image": "https://your-bucket.s3.your-region.amazonaws.com/ican/1/image.jpg",
     *     "price": 99.99,
     *     "price_type": "fixed",
     *     "status": "active",
     *     "location": "Dubai",
     *     "experience": "5 years",
     *     "created_at": "2024-06-05T12:00:00.000000Z",
     *     "updated_at": "2024-06-05T12:00:00.000000Z"
     *   }
     * }
     * @param Request $request
     * @param int $ican_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $ican_id)
    {
        $request->validate([
            'title' => 'sometimes|string|max:255',
            'short_description' => 'sometimes|string|max:255',
            'image' => 'nullable|image|max:25600', // 25MB max size
            'price' => 'sometimes|numeric',
            'price_type' => 'sometimes|in:fixed,hourly',
            'status' => 'sometimes|in:active,inactive',
            'location' => 'nullable|string|max:255',
            'experience' => 'nullable|string|max:255'
        ]);

        $post = ICan::findOrFail($ican_id);
        $post->update($request->except('image'));

        if ($request->hasFile('image')) {
            // Delete the old image from S3
            if ($post->image) {
                Storage::disk('s3')->delete(parse_url($post->image, PHP_URL_PATH));
            }

            // Store the new image
            $path = $request->file('image')->store("ican/{$post->id}", 's3');
            $post->image = Storage::disk('s3')->url($path);
            $post->save();
        }

        return response()->json(['message' => 'Post updated successfully', 'post' => $post]);
    }

    /**
     * Remove the specified ICan post from storage.
     *
     * @group ICan Posts
     * @urlParam ican_id int required The ID of the post. Example: 1
     * @response 200 {
     *   "message": "Post deleted successfully"
     * }
     * @param int $ican_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($ican_id)
    {
        $post = ICan::findOrFail($ican_id);

        // Delete the image from S3
        if ($post->image) {
            Storage::disk('s3')->delete(parse_url($post->image, PHP_URL_PATH));
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }
}