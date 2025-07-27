<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostLike;
use Illuminate\Http\Request;

class PostLikeController extends Controller
{
    public function like(Request $request, Post $post)
    {
        $like = PostLike::firstOrCreate([
            'user_id' => $request->user()->id,
            'post_id' => $post->id,
        ]);
        return response()->json(['message' => 'Post liked']);
    }

    public function unlike(Request $request, Post $post)
    {
        PostLike::where('user_id', $request->user()->id)
            ->where('post_id', $post->id)
            ->delete();

        return response()->json(['message' => 'Post unliked']);
    }
}
