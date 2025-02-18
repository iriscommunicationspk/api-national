<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    //


    public function Login(Request $request)
    {

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        if (Auth::guard('web')->attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::guard('web')->user();

            $expiresAt = Carbon::now('Asia/Karachi')->addHours(24);
            // Create token with expiration
            $token = $user->createToken('SPA-Token', ['*'], $expiresAt)->plainTextToken;

            return response()->json([
                'user' => $user,
                'token' => $token,
                'success' => 'authenticated'
            ]);
        }

        // Return a validation error for failed login attempt
        throw ValidationException::withMessages([
            'message' => ['The provided credentials are incorrect.'],
        ]);
    }

    public function logout(Request $request)
    {
        // Revoke the token that was used to authenticate the current request
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.'
        ], 200);
    }

    public function logoutFromAll(Request $request)
    {
        // Revoke all tokens for the authenticated user
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out from all devices successfully.'
        ], 200);
    }
}
