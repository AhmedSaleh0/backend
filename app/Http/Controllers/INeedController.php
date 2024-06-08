<?php

namespace App\Http\Controllers;

use App\Models\INeed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class INeedController extends Controller
{
    /**
     * Display a listing of INeed posts.
     *
     * @group INeed Posts
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $posts = INeed::all();
        return response()->json($posts);
    }

    /**
     * Store a newly created INeed post in storage.
     *
     * @group INeed Posts
     * @bodyParam post_title string required The title of the post. Example: My New Request
     * @bodyParam post_short_description string required A short description of the post. Example: This is a short description of my request.
     * @bodyParam post_image file nullable An image associated with the post (max 25MB).
     * @bodyParam post_price numeric required The price of the request. Example: 99.99
     * @bodyParam post_price_type string required The type of pricing (fixed or hourly). Example: fixed
     * @bodyParam post_status string required The status of the post (active or inactive). Example: active
     * @response 201 {
     *   "message": "Post created successfully",
     *   "post": {
     *     "id": 1,
     *     "post_title": "My New Request",
     *     "post_short_description": "This is a short description of my request.",
     *     "post_image": "https://your-bucket.s3.your-region.amazonaws.com/ineed/1/image.jpg",
     *     "post_price": 99.99,
     *     "post_price_type": "fixed",
     *     "post_status": "active",
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
            'post_title' => 'required|string|max:255',
            'post_short_description' => 'required|string|max:255',
            'post_image' => 'nullable|image|max:25600', // 25MB max size
            'post_price' => 'required|numeric',
            'post_price_type' => 'required|in:fixed,hourly',
            'post_status' => 'required|in:active,inactive'
        ]);

        $post = INeed::create($request->except('post_image'));

        if ($request->hasFile('post_image')) {
            $path = $request->file('post_image')->store("ineed/{$post->id}", 's3');
            $post->post_image = Storage::disk('s3')->url($path);
            $post->save();
        }

        return response()->json(['message' => 'Post created successfully', 'post' => $post], 201);
    }

    /**
     * Display the specified INeed post.
     *
     * @group INeed Posts
     * @urlParam id int required The ID of the post. Example: 1
     * @response 200 {
     *   "id": 1,
     *   "post_title": "My New Request",
     *   "post_short_description": "This is a short description of my request.",
     *   "post_image": "https://your-bucket.s3.your-region.amazonaws.com/ineed/1/image.jpg",
     *   "post_price": 99.99,
     *   "post_price_type": "fixed",
     *   "post_status": "active",
     *   "created_at": "2024-06-05T12:00:00.000000Z",
     *   "updated_at": "2024-06-05T12:00:00.000000Z"
     * }
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $post = INeed::findOrFail($id);
        return response()->json($post);
    }

    /**
     * Update the specified INeed post in storage.
     *
     * @group INeed Posts
     * @urlParam id int required The ID of the post. Example: 1
     * @bodyParam post_title string The title of the post. Example: My Updated Request
     * @bodyParam post_short_description string A short description of the post. Example: This is an updated short description of my request.
     * @bodyParam post_image file An image associated with the post (max 25MB).
     * @bodyParam post_price numeric The price of the request. Example: 99.99
     * @bodyParam post_price_type string The type of pricing (fixed or hourly). Example: fixed
     * @bodyParam post_status string The status of the post (active or inactive). Example: active
     * @response 200 {
     *   "message": "Post updated successfully",
     *   "post": {
     *     "id": 1,
     *     "post_title": "My Updated Request",
     *     "post_short_description": "This is an updated short description of my request.",
     *     "post_image": "https://your-bucket.s3.your-region.amazonaws.com/ineed/1/image.jpg",
     *     "post_price": 99.99,
     *     "post_price_type": "fixed",
     *     "post_status": "active",
     *     "created_at": "2024-06-05T12:00:00.000000Z",
     *     "updated_at": "2024-06-05T12:00:00.000000Z"
     *   }
     * }
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'post_title' => 'sometimes|string|max:255',
            'post_short_description' => 'sometimes|string|max:255',
            'post_image' => 'nullable|image|max:25600', // 25MB max size
            'post_price' => 'sometimes|numeric',
            'post_price_type' => 'sometimes|in:fixed,hourly',
            'post_status' => 'sometimes|in:active,inactive'
        ]);

        $post = INeed::findOrFail($id);
        $post->update($request->except('post_image'));

        if ($request->hasFile('post_image')) {
            // Delete the old image from S3
            if ($post->post_image) {
                Storage::disk('s3')->delete(parse_url($post->post_image, PHP_URL_PATH));
            }

            // Store the new image
            $path = $request->file('post_image')->store("ineed/{$post->id}", 's3');
            $post->post_image = Storage::disk('s3')->url($path);
            $post->save();
        }

        return response()->json(['message' => 'Post updated successfully', 'post' => $post]);
    }

    /**
     * Remove the specified INeed post from storage.
     *
     * @group INeed Posts
     * @urlParam id int required The ID of the post. Example: 1
     * @response 200 {
     *   "message": "Post deleted successfully"
     * }
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $post = INeed::findOrFail($id);

        // Delete the image from S3
        if ($post->post_image) {
            Storage::disk('s3')->delete(parse_url($post->post_image, PHP_URL_PATH));
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }
}
