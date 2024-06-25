<?php

namespace App\Http\Controllers\ICan;

use App\Models\ICan\ICan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
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
        $posts = ICan::with(['user', 'user.image', 'skills'])->get()->map(function ($post) {
            $post->liked_by_user = Auth::check() ? $post->isLikedByUser() : false;
            return $post;
        });
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
     * @bodyParam location string nullable The location of the post. Example: Dubai
     * @bodyParam experience string nullable The experience required for the post. Example: Intermediate
     * @bodyParam skills array nullable The skills associated with the post. Example: [1, 2, 3]
     * @response 201 {
     *   "message": "Post created successfully",
     *   "post": {
     *     "id": 1,
     *     "title": "My New Service",
     *     "short_description": "This is a short description of my service.",
     *     "image": "https://your-bucket.s3.your-region.amazonaws.com/ican/1/image.jpg",
     *     "price": 99.99,
     *     "price_type": "fixed",
     *     "status": "pending",
     *     "location": "Dubai",
     *     "experience": "Intermediate",
     *     "skills": [1, 2, 3],
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
            'location' => 'nullable|string|max:255',
            'experience' => 'nullable|in:Entry,Intermediate,Expert',
            'skills' => 'nullable|array',
            'skills.*' => 'exists:skills,id',
        ]);

        $post = ICan::create($request->except('image', 'skills') + ['status' => 'pending', 'user_id' => Auth::id()]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store("ican/{$post->id}", 's3');
            $post->image = Storage::disk('s3')->url($path);
            $post->save();
        }

        // Attach skills to the post
        if ($request->has('skills')) {
            $post->skills()->attach($request->skills);
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
     *   "status": "pending",
     *   "location": "Dubai",
     *   "experience": "Intermediate",
     *   "skills": [1, 2, 3],
     *   "user": {
     *     "id": 1,
     *     "first_name": "John",
     *     "last_name": "Doe",
     *     "email": "john.doe@example.com",
     *     "phone": "+1234567890",
     *     "country_code": "+1",
     *     "username": "johndoe",
     *     "country": "USA",
     *     "birthdate": "1990-01-01",
     *     "bio": "A short bio about John Doe.",
     *     "image": {
     *       "id": 1,
     *       "url": "https://your-bucket.s3.your-region.amazonaws.com/users/1/image.jpg"
     *     }
     *   }
     * }
     * @param int $ican_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($ican_id)
    {
        $post = ICan::with(['user', 'user.image', 'skills'])->findOrFail($ican_id);
        $post->liked_by_user = Auth::check() ? $post->isLikedByUser() : false;
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
     * @bodyParam experience string The experience required for the post. Example: Intermediate
     * @bodyParam skills array nullable The skills associated with the post. Example: [1, 2, 3]
     * @response 200 {
     *   "message": "Post updated successfully",
     *   "post": {
     *     "id": 1,
     *     "title": "My Updated Service",
     *     "short_description": "This is an updated short description of my service.",
     *     "image": "https://your-bucket.s3.your-region.amazonaws.com/ican/1/image.jpg",
     *     "price": 99.99,
     *     "price_type": "fixed",
     *     "status": "pending",
     *     "location": "Dubai",
     *     "experience": "Intermediate",
     *     "skills": [1, 2, 3],
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
            'location' => 'nullable|string|max:255',
            'experience' => 'nullable|in:Entry,Intermediate,Expert',
            'skills' => 'nullable|array',
            'skills.*' => 'exists:skills,id',
        ]);

        $post = ICan::findOrFail($ican_id);
        $post->update($request->except('image', 'skills') + ['status' => 'pending']);

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

        // Sync skills
        if ($request->has('skills')) {
            $post->skills()->sync($request->skills);
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

        // Detach skills
        $post->skills()->detach();

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }

    /**
     * Display a listing of the authenticated user's ICan posts.
     *
     * @group ICan Posts
     * @return \Illuminate\Http\JsonResponse
     */
    public function myIcan()
    {
        $posts = ICan::with(['user', 'user.image', 'skills'])->where('user_id', Auth::id())->get()->map(function ($post) {
            $post->liked_by_user = Auth::check() ? $post->isLikedByUser() : false;
            return $post;
        });
        return response()->json($posts);
    }
}
