<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CommentsController extends Controller
{
    use AuthorizesRequests;

    public function index(Post $post)
    {
        $comments = $post->comments() // already filters top-level only
            ->with(['user', 'replies.user']) // eager load
            ->get();

        return response()->json([
            'comments' => $comments
        ]);
    }


    public function store(Request $request, Post $post)
    {
        $validated = $request->validate([
            'content' => 'required|string'
        ]);
        $comment = Comments::create([
            'post_id' => $post->id,
            'user_id' => $request->user()->id,
            'content' => $validated['content'],
        ]);

        return response()->json([
            'message' => 'Comment created successfully',
            'comment' => $comment
        ], 201);
    }

    public function reply(Request $request, Comments $comment)
    {
        $request->validate(['content' => 'required|string']);
        return Comments::create([
            'post_id' => $comment->post_id,
            'user_id' => $request->user()->id,
            'content' => $request->content,
            'parent_id' => $comment->id,
        ]);
    }

    public function update(Request $request, Comments $comment)
    {
        $this->authorize('update', $comment); // Optional: authorization
        $request->validate(['content' => 'required|string']);
        $comment->update(['content' => $request->content]);
        return response()->json(['message' => 'Comment updated', 'comment' => $comment]);
    }

    public function destroy(Comments $comment)
    {
        $this->authorize('delete', $comment); // Optional: authorization
        $comment->delete();
        return response()->json(['message' => 'Comment deleted']);
    }
}
