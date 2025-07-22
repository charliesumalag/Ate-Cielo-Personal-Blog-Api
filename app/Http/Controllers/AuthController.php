<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:100',
            'username' => 'required|max:100|unique:users',
            'password' => 'required|confirmed',
            'image' => 'nullable|max:2048',
        ]);
        Log::info($validated);
        $validated['password'] = bcrypt($validated['password']);

        $imagePath = null;


        // if ($request->hasFile('image')) {
        //     $imagePath = $request->file('image')->store('profile', 'public');
        //     $validated['image_path'] = $imagePath;
        // }
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('uploads/profile', 'public');
            $validated['image_path'] = $imagePath;
        } else {
            $validated['image_path'] = 'uploads/profile/default.jpg';
        }
        $user = User::create($validated);
        $token = $user->createToken($request->name);
        return [
            'user' => $user,
            'token' => $token->plainTextToken,
        ];
    }
    public function login(Request $request)
    {

        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'errors' => ['backerror' => 'The provided credentials are incorrect.']
            ], 422);
        }

        $token = $user->createToken($user->name);

        return [
            'user' => $user,
            'token' => $token->plainTextToken,
        ];
    }
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return [
            'message' => 'You are logged out'
        ];
    }


    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'string|max:100',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('uploads/profile', 'public');
            $validated['image_path'] = $imagePath;
        }

        $user = Auth::user();
        $user->update([
            'name' => $validated['name'],
            'image_path' => $validated['image_path'] ?? $user->image_path,
        ]);


        return [
            'user' => $user,
        ];
    }
    public function dashboard(Request $request)
    {
        return response()->json([
            'message' => 'Welcome to the admin dashboard!',
            'user' => $request->user(),
            // Add stats/data here if needed
        ]);
    }
}
