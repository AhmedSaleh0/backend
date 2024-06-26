<?php

namespace App\Http\Controllers\Inspire;

use App\Models\Inspire\Inspire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class InspireController extends Controller
{
    /**
     * Display a listing of Inspire posts.
     *
     * @group Inspire Posts
     * @response 200 {
     *   "id": 1,
     *   "type": "image",
     *   "title": "My New Post",
     *   "content": "This is the content of my post.",
     *   "media_url": "https://your-bucket.s3.your-region.amazonaws.com/inspire/1/media.jpg",
     *   "user_id": 1,
     *   "status": "active",
     *   "views": 100,
     *   "category": 1,
     *   "sub_category": 2,
     *   "liked_by_user": true,
     *   "created_at": "2024-06-05T12:00:00.000000Z",
     *   "updated_at": "2024-06-05T12:00:00.000000Z"
     * }
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $posts = Inspire::with(['user', 'user.image', 'category', 'subCategory'])->get()->map(function ($post) {
            $post->liked_by_user = Auth::check() ? $post->isLikedByUser() : false;
            return $post;
        });
        return response()->json($posts);
    }

    /**
     * Store a newly created Inspire post in storage.
     *
     * @group Inspire Posts
     * @param Request $request
     * @bodyParam title string required The title of the post. Example: My New Post
     * @bodyParam content string required The content of the post. Example: This is the content of my post.
     * @bodyParam media file required The media file associated with the post.
     * @bodyParam type string required The type of the media (video or image). Example: video
     * @bodyParam category integer required The ID of the category. Example: 1
     * @bodyParam sub_category integer required The ID of the sub-category. Example: 2
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'media' => 'required|file|mimetypes:image/jpeg,image/png,video/mp4,video/x-msvideo,video/x-matroska|max:25600',  // Max 25MB file
            'type' => 'required|in:video,image',
            'category' => 'required|integer|exists:skills_categories,id',
            'sub_category' => 'required|integer|exists:skills_sub_categories,id',
        ]);

        // Store the media file in the 'inspire' folder in S3
        $path = $request->file('media')->store('inspire', 's3');

        $post = Inspire::create([
            'type' => $request->type,
            'title' => $request->title,
            'content' => $request->content,
            'media_url' => Storage::disk('s3')->url($path),
            'user_id' => Auth::id(),
            'category' => $request->category,
            'sub_category' => $request->sub_category,
            'status' => 'pending',  // Default status
            'views' => 0,  // Default views
        ]);

        return response()->json([
            'message' => 'Post created successfully',
            'post' => $post
        ], 201);
    }

    /**
     * Display the specified Inspire post.
     *
     * @group Inspire Posts
     * @urlParam inspire_id int required The ID of the post. Example: 1
     * @response 200 {
     *   "id": 1,
     *   "type": "image",
     *   "title": "My New Post",
     *   "content": "This is the content of my post.",
     *   "media_url": "https://your-bucket.s3.your-region.amazonaws.com/inspire/1/media.jpg",
     *   "user_id": 1,
     *   "status": "active",
     *   "views": 100,
     *   "category": 1,
     *   "sub_category": 2,
     *   "liked_by_user": true,
     *   "created_at": "2024-06-05T12:00:00.000000Z",
     *   "updated_at": "2024-06-05T12:00:00.000000Z",
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
     * @param int $inspire_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($inspire_id)
    {
        $post = Inspire::with(['user', 'user.image', 'category', 'subCategory'])->findOrFail($inspire_id);
        $post->increment('views');  // Increment view count upon retrieval
        $post->liked_by_user = Auth::check() ? $post->isLikedByUser() : false;
        return response()->json($post);
    }

    /**
     * Update the specified Inspire post in storage.
     *
     * @group Inspire Posts
     * @param Request $request
     * @param int $inspire_id
     * @bodyParam title string The title of the post. Example: My Updated Post
     * @bodyParam content string The content of the post. Example: This is the updated content of my post.
     * @bodyParam media file The media file associated with the post.
     * @bodyParam type string The type of the media (video or image). Example: image
     * @bodyParam category integer The ID of the category. Example: 1
     * @bodyParam sub_category integer The ID of the sub-category. Example: 2
     * @bodyParam status string The status of the post (active or inactive). Example: active
     * @urlParam inspire_id int required The ID of the post. Example: 1
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $inspire_id)
    {
        $request->validate([
            'title' => 'sometimes|string|max:255',
            'type' => 'sometimes|in:video,image',
            'content' => 'sometimes|string',
            'media' => 'nullable|file|mimetypes:image/jpeg,image/png,video/mp4,video/x-msvideo,video/x-matroska|max:25600',
            'category' => 'sometimes|integer|exists:skills_categories,id',
            'sub_category' => 'sometimes|integer|exists:skills_sub_categories,id',
            'status' => 'sometimes|in:active,inactive',
        ]);

        $post = Inspire::findOrFail($inspire_id);

        // Check if the user is authorized to update the post
        if ($post->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized to update this post.'], 403);
        }

        if ($request->hasFile('media')) {
            // Delete the old media from S3
            Storage::disk('s3')->delete(parse_url($post->media_url, PHP_URL_PATH));

            // Store the new media
            $path = $request->file('media')->store('inspire', 's3');
            $post->update([
                'media_url' => Storage::disk('s3')->url($path)
            ]);
        }

        $post->update($request->except(['media', 'user_id']) + ['status' => 'pending']);

        return response()->json([
            'message' => 'Post updated successfully',
            'post' => $post
        ]);
    }

    /**
     * Remove the specified Inspire post from storage.
     *
     * @group Inspire Posts
     * @urlParam inspire_id int required The ID of the post. Example: 1
     * @return \Illuminate\Http\Response
     */
    public function destroy($inspire_id)
    {
        $post = Inspire::findOrFail($inspire_id);

        // Check if the user is authorized to delete the post
        if ($post->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized to delete this post.'], 403);
        }

        // Delete the media from S3
        Storage::disk('s3')->delete(parse_url($post->media_url, PHP_URL_PATH));

        // Delete the record from the database
        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }

    /**
     * Display a listing of the authenticated user's Inspire posts.
     *
     * @group Inspire Posts
     * @return \Illuminate\Http\JsonResponse
     */
    public function myInspire()
    {
        $posts = Inspire::with(['user', 'user.image', 'category', 'subCategory'])->where('user_id', Auth::id())->get()->map(function ($post) {
            $post->liked_by_user = Auth::check() ? $post->isLikedByUser() : false;
            return $post;
        });
        return response()->json($posts);
    }

    /**
     * Display a listing of Inspire posts by a specific user.
     *
     * @group Inspire Posts
     * @urlParam user_id int required The ID of the user. Example: 1
     * @response 200 {
     *   "id": 1,
     *   "type": "image",
     *   "title": "My New Post",
     *   "content": "This is the content of my post.",
     *   "media_url": "https://your-bucket.s3.your-region.amazonaws.com/inspire/1/media.jpg",
     *   "user_id": 1,
     *   "status": "active",
     *   "views": 100,
     *   "category": 1,
     *   "sub_category": 2,
     *   "liked_by_user": true,
     *   "created_at": "2024-06-05T12:00:00.000000Z",
     *   "updated_at": "2024-06-05T12:00:00.000000Z",
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
     * @param int $user_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function userInspire($user_id)
    {
        $posts = Inspire::with(['user', 'user.image', 'category', 'subCategory'])
                        ->where('user_id', $user_id)
                        ->get()
                        ->map(function ($post) {
                            $post->liked_by_user = Auth::check() ? $post->isLikedByUser() : false;
                            return $post;
                        });
        return response()->json($posts);
    }
}
