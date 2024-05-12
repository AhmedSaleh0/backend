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
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'video_url' => 'nullable|url',
            'category' => 'required|string',
            'sub_category' => 'required|string',
        ]);

        $post = Inspire::create([
            'title' => $request->title,
            'content' => $request->content,
            'video_url' => $request->video_url,
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
     * @param int $id
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
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'video_url' => 'nullable|url',
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
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Inspire::findOrFail($id);
        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }
}
