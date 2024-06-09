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
     * @unauthenticated
     * 
     * @group Inspire Posts
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Inspire::all();
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
            'status' => 'active',  // Default status
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
     * @urlParam id int required The ID of the post. Example: 1
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Inspire::findOrFail($id);
        $post->increment('views');  // Increment view count upon retrieval
        return response()->json($post);
    }

    /**
     * Update the specified Inspire post in storage.
     *
     * @group Inspire Posts
     * @param Request $request
     * @param int $id
     * @bodyParam title string The title of the post. Example: My Updated Post
     * @bodyParam content string The content of the post. Example: This is the updated content of my post.
     * @bodyParam media file The media file associated with the post.
     * @bodyParam type string The type of the media (video or image). Example: image
     * @bodyParam category integer The ID of the category. Example: 1
     * @bodyParam sub_category integer The ID of the sub-category. Example: 2
     * @bodyParam status string The status of the post (active or inactive). Example: active
     * @urlParam id int required The ID of the post. Example: 1
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
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

        $post = Inspire::findOrFail($id);

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

        $post->update($request->except(['media', 'user_id']));

        return response()->json([
            'message' => 'Post updated successfully',
            'post' => $post
        ]);
    }

    /**
     * Remove the specified Inspire post from storage.
     *
     * @group Inspire Posts
     * @urlParam id int required The ID of the post. Example: 1
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Inspire::findOrFail($id);

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
}
