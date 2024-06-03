<?php

namespace App\Http\Controllers;

use App\Models\InspireComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InspireCommentController extends Controller
{
    /**
     * Display a listing of comments for a specific Inspire post.
     *
     * @group Inspire Comments
     * @param int $inspire_id
     * @return \Illuminate\Http\Response
     */
    public function index($inspire_id)
    {
        $comments = InspireComment::where('inspire_id', $inspire_id)->get();
        return response()->json($comments);
    }

    /**
     * Store a newly created comment in storage.
     *
     * @group Inspire Comments
     * @param Request $request
     * @param int $inspire_id
     * @bodyParam comment string required The comment text. Example: This is a great post!
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $inspire_id)
    {
        $request->validate([
            'comment' => 'required|string',
        ]);

        $comment = InspireComment::create([
            'inspire_id' => $inspire_id,
            'user_id' => Auth::id(),
            'comment' => $request->comment,
        ]);

        return response()->json([
            'message' => 'Comment added successfully',
            'comment' => $comment
        ], 201);
    }

    /**
     * Display the specified comment.
     *
     * @group Inspire Comments
     * @urlParam id int required The ID of the comment. Example: 1
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $comment = InspireComment::findOrFail($id);
        return response()->json($comment);
    }

    /**
     * Update the specified comment in storage.
     *
     * @group Inspire Comments
     * @param Request $request
     * @param int $id
     * @bodyParam comment string required The comment text. Example: This is an updated comment!
     * @urlParam id int required The ID of the comment. Example: 1
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'comment' => 'required|string',
        ]);

        $comment = InspireComment::findOrFail($id);
        $comment->update($request->all());

        return response()->json([
            'message' => 'Comment updated successfully',
            'comment' => $comment
        ]);
    }

    /**
     * Remove the specified comment from storage.
     *
     * @group Inspire Comments
     * @urlParam id int required The ID of the comment. Example: 1
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $comment = InspireComment::findOrFail($id);
        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully']);
    }
}
