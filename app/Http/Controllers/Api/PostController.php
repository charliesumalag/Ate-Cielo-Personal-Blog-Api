<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
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
        $posts = Post::with('user')->latest()->get();
        return response()->json([
            'posts' => $posts
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string',
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:posts,slug',
            'description' => 'required|string',
            'tags' => 'nullable|string',
            'image' => 'nullable|max:2048',
            'published' => 'boolean',
        ]);
        $validated['published_at'] = !empty($validated['published']) ? now() : null;

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('uploads/blog', 'public');
            $validated['image_path'] = $imagePath;
        }
        // $post = Post::create($validated);
        $post = $request->user()->posts()->create($validated);
        $post->load('user');

        return response()->json([
            'post' => $post,
            'message' => 'Posted'
        ], 201);
    }


    public function show(Post $post)
    {
        return ['post' => $post, 'user' => $post->user];
    }

    public function update(Request $request, Post $post)
    {
        // Gate::authorize('modify', $post);

        $validated = $request->validate([
            'category' => 'required|string',
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:posts,slug,' . $post->id, // <== Ignore current post
            'description' => 'required|string',
            'tags' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'published' => 'boolean',
        ]);

        $validated['published_at'] = $validated['published'] ? now() : null;

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('uploads/blog', 'public');
        }

        $post->update($validated);

        return response()->json([
            'post' => $post->fresh(),
            'message' => 'Post updated successfully.',
        ]);
    }

    public function getPostsByUser($userId)
    {
        $posts = \App\Models\Post::where('user_id', $userId)->latest()->get();
        return response()->json($posts);
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
