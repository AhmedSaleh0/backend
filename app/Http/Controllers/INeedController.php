<?php
 

namespace App\Http\Controllers;

use App\Models\INeed;
use Illuminate\Http\Request;

class INeedController extends Controller
{
    public function index()
    {
        $posts = INeed::all();
        return response()->json($posts);
    }

    public function store(Request $request)
    {
        $request->validate([
            'post_title' => 'required|string|max:255',
            'post_short_description' => 'required|string|max:255',
            'post_image' => 'nullable|image|max:2048', // 2MB max size
            'post_price' => 'required|numeric',
            'post_price_type' => 'required|in:fixed,hourly',
            'post_status' => 'required|in:active,inactive'
        ]);

        $post = INeed::create($request->all());

        return response()->json(['message' => 'Post created successfully', 'post' => $post], 201);
    }

    public function show($id)
    {
        $post = INeed::findOrFail($id);
        return response()->json($post);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'post_title' => 'sometimes|string|max:255',
            'post_short_description' => 'sometimes|string|max:255',
            'post_image' => 'nullable|image|max:2048',
            'post_price' => 'sometimes|numeric',
            'post_price_type' => 'sometimes|in:fixed,hourly',
            'post_status' => 'sometimes|in:active,inactive'
        ]);

        $post = INeed::findOrFail($id);
        $post->update($request->all());

        return response()->json(['message' => 'Post updated successfully', 'post' => $post]);
    }

    public function destroy($id)
    {
        INeed::destroy($id);
        return response()->json(['message' => 'Post deleted successfully']);
    }
}
