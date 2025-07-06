<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class PostController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show']),
        ];
    }
    public function index(Request $request)
    {
        return Post::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:posts,slug',
            'content' => 'required|string',
            'image' => 'nullable|max:2048',
            'published' => 'boolean',
        ]);
        $validated['published_at'] = $validated['published'] ? now() : null;

        // $post = Post::create($validated);
        $post = $request->user()->posts()->create($validated);

        return response()->json($post, 201);
    }


    public function show(Post $post)
    {
        return $post;
    }

    public function update(Request $request, Post $post)
    {
        Gate::authorize('modify', $post);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:posts,slug',
            'content' => 'required|string',
            'image' => 'nullable|max:2048',
            'published' => 'boolean',
        ]);
        $validated['published_at'] = $validated['published'] ? now() : null;
        $post = Post::findOrFail($post);
        $post->update($validated);    //
        return response()->json($post->fresh(), 200); // 👈 return updated post
    }


    public function destroy(Post $post)
    {
        Gate::authorize('modify', $post);
        $post->delete();
        return [
            'message' => 'The post was deleted',
        ];
    }
}
