<?php

namespace App\Http\Controllers;

use App\Models\Inspire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InspireController extends Controller
{
    /**
     * Display a listing of Inspire posts.
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
     * @bodyParam media_url string nullable The URL of the media associated with the post. Example: http://example.com/media.mp4
     * @bodyParam type string required The type of the media (video or image). Example: video
     * @bodyParam category string required The category of the post. Example: Technology
     * @bodyParam sub_category string required The sub-category of the post. Example: AI
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'media_url' => 'nullable|url',
            'type' => 'required|in:video,image',
            'category' => 'required|string',
            'sub_category' => 'required|string',
        ]);

        $post = Inspire::create([
            'type' => $request->type,
            'title' => $request->title,
            'content' => $request->content,
            'media_url' => $request->media_url,
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
     * @bodyParam media_url string The URL of the media associated with the post. Example: http://example.com/media.mp4
     * @bodyParam type string The type of the media (video or image). Example: image
     * @bodyParam category string The category of the post. Example: Technology
     * @bodyParam sub_category string The sub-category of the post. Example: AI
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
            'media_url' => 'nullable|url',
            'category' => 'sometimes|string',
            'sub_category' => 'sometimes|string',
            'status' => 'sometimes|in:active,inactive',
        ]);

        $post = Inspire::findOrFail($id);
        $post->update($request->all());

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
        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }
}
