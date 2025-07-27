<?php

namespace App\Http\Controllers;

use App\Models\Comments;
use App\Models\CommentLike;
use Illuminate\Http\Request;

class CommentLikeController extends Controller
{
    public function like(Request $request, Comments $comment)
    {
        $like = CommentLike::firstOrCreate([
            'user_id' => $request->user()->id,
            'comment_id' => $comment->id,
        ]);
        return response()->json(['message' => 'Comment liked']);
    }

    public function unlike(Request $request, Comments $comment)
    {
        CommentLike::where('user_id', $request->user()->id)
            ->where('comment_id', $comment->id)
            ->delete();

        return response()->json(['message' => 'Comment unliked']);
    }
}
