<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Register a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create the user with a default role of 'user'
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'user',  // Assign default role
        ]);

        // Generate access token for the user
        $accessToken = $user->createToken('authToken')->accessToken;

        // Return success response with access token
        return response()->json(['message' => 'User registered successfully', 'access_token' => $accessToken, 'user' => $user], 201);
    }

    /**
     * Authenticate user and generate access token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // Validate login request
        $credentials = $request->only('email', 'password');

        // Attempt to log in the user
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('authToken')->accessToken;

            // Return success response with access token and user role
            return response()->json([
                'message' => 'Login successful',
                'access_token' => $token,
                'role' => $user->role,  // Include the user's role in the response
            ], 200);
        } else {
            // If authentication fails, return unauthorized response
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    }

    /**
     * Get authenticated user information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function userInfo(Request $request)
    {
        // Get the authenticated user
        $user = Auth::user();

        // Return user information
        return response()->json(['user' => $user], 200);
    }

    /**
     * Logout the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Check if the user is authenticated
        if (Auth::check()) {
            // Logout the user
            Auth::logout();

            // Return success response
            return response()->json(['message' => 'User logged out successfully'], 200);
        }

        // If user is not authenticated, return error response
        return response()->json(['message' => 'User not logged in'], 401);
    }
}
