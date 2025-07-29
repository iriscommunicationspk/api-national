<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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

            $token = $this->generateUserToken($user);

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

    /**
     * Generate a token for the given user.
     *
     * @param User $user
     * @return string
     */
    protected function generateUserToken($user)
    {
        // Delete existing tokens to prevent token buildup
        $user->tokens()->delete();

        // Create a new token
        return $user->createToken('auth-token')->plainTextToken;
    }

    /**
     * Redirect the user to the Office 365 authentication page.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    // public function redirectToOffice365()
    // {
    //     try {
    //         $redirectUrl = Socialite::driver('microsoft')->redirect()->getTargetUrl();

    //         return response()->json([
    //             'redirectUrl' => $redirectUrl,
    //             'status' => 'success'
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' => 'Could not connect to Office 365: ' . $e->getMessage(),
    //             'status' => 'error'
    //         ], 500);
    //     }
    // }

    // /**
    //  * Handle Office 365 callback and authenticate the user.
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
  public function handleOffice365Callback()
{
    try {
        // Retrieve user from Microsoft using Socialite
        $microsoftUser = Socialite::driver('microsoft')->stateless()->user();

        // Find existing user by email
        $user = User::where('email', $microsoftUser->getEmail())->first();

        if (!$user) {
            // Create a new user if not found
            $user = User::create([
                'name' => $microsoftUser->getName() ?? 'Microsoft User',
                'email' => $microsoftUser->getEmail(),
                'password' => Hash::make(Str::random(16)),
                'microsoft_id' => $microsoftUser->getId(),
            ]);
        } else {
            // Update microsoft_id if missing
            if (empty($user->microsoft_id)) {
                $user->microsoft_id = $microsoftUser->getId();
                $user->save();
            }
        }

        // Generate token (assuming you have this method)
        $token = $this->generateUserToken($user);

        // Frontend redirect URL
        $frontendUrl = config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:5173'));

        return redirect()->away($frontendUrl . '/auth-callback?token=' . $token . '&user=' . urlencode(json_encode([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ])));

    } catch (\Exception $e) {
        // Redirect to frontend with error message
        $frontendUrl = config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:5173'));
        return redirect()->away($frontendUrl . '/login?error=' . urlencode("OAuth Failed: " . $e->getMessage()));
    }
}



// public function handleOffice365Callback()
// {
//     try {
//         $microsoftUser = Socialite::driver('microsoft')->stateless()->user();

//         // Dump the user object temporarily to inspect it
//         dd($microsoftUser);

//     } catch (\Exception $e) {
//         \Log::error('Microsoft OAuth Error', [
//             'message' => $e->getMessage(),
//             'trace' => $e->getTraceAsString(),
//             'code' => $e->getCode(),
//             'url' => request()->fullUrl(),
//         ]);

//         return response()->json(['error' => 'OAuth Failed: ' . $e->getMessage()], 500);
//     }
// }

    public function redirectToOffice365()
    {
        return Socialite::driver('microsoft')->redirect();
    }

    // public function handleOffice365Callback(Request $request)
    // {
    //     try {
    //         // Optional: log for debug
    //         // \Log::info('Microsoft callback response', $request->all());

    //         // This line triggers the token exchange with Microsoft
    //         $microsoftUser = Socialite::driver('microsoft')->user();

    //         // Create or find user
    //         $user = User::firstOrCreate(
    //             ['email' => $microsoftUser->getEmail()],
    //             [
    //                 'name' => $microsoftUser->getName(),
    //                 'password' => bcrypt(str()->random(16)), // placeholder
    //             ]
    //         );

    //         // Login
    //         Auth::login($user);

    //         return redirect('/dashboard');
    //     } catch (\Exception $e) {
    //         // dd($e);

    //         return redirect('/login')->with('error', 'Office 365 login failed: ' . $e->getMessage());
    //     }
    // }

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
