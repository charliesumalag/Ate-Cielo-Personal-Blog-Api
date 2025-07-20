<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:100',
            'username' => 'required|max:100|unique:users',
            'password' => 'required|confirmed',
        ]);
        Log::info($validated);
        $validated['password'] = bcrypt($validated['password']);

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


    public function dashboard(Request $request)
    {
        return response()->json([
            'message' => 'Welcome to the admin dashboard!',
            'user' => $request->user(),
            // Add stats/data here if needed
        ]);
    }
}
