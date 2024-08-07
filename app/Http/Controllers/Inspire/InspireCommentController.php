<?php

namespace App\Http\Controllers\Inspire;

use App\Models\Inspire\InspireComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class InspireCommentController extends Controller
{
    /**
     * Display a listing of comments for a specific Inspire post.
     *
     * @unauthenticated
     * 
     * @group iNspire Comments
     * @param int $inspire_id
     * @return \Illuminate\Http\Response
     */
    public function index($inspire_id)
    {
        $comments = InspireComment::with(['user', 'user.image'])->where('inspire_id', $inspire_id)->get();
        return response()->json($comments);
    }

    /**
     * Store a newly created comment in storage.
     *
     * @group iNspire Comments
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
     * @group iNspire Comments
     * @urlParam comment_id int required The ID of the comment. Example: 1
     * @return \Illuminate\Http\Response
     */
    public function show($comment_id)
    {
        $comment = InspireComment::with(['user', 'user.image'])->findOrFail($comment_id);
        return response()->json($comment);
    }

    /**
     * Update the specified comment in storage.
     *
     * @group iNspire Comments
     * @param Request $request
     * @param int $comment_id
     * @bodyParam comment string required The comment text. Example: This is an updated comment!
     * @urlParam comment_id int required The ID of the comment. Example: 1
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $comment_id)
    {
        $request->validate([
            'comment' => 'required|string',
        ]);

        $comment = InspireComment::findOrFail($comment_id);
        $comment->update($request->all());

        return response()->json([
            'message' => 'Comment updated successfully',
            'comment' => $comment
        ]);
    }

    /**
     * Remove the specified comment from storage.
     *
     * @group iNspire Comments
     * @urlParam comment_id int required The ID of the comment. Example: 1
     * @return \Illuminate\Http\Response
     */
    public function destroy($comment_id)
    {
        $comment = InspireComment::findOrFail($comment_id);
        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully']);
    }
}
