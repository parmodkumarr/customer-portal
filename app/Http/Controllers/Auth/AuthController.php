<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Generate MFA token
        $mfaToken = Str::random(6);
        $user->mfa_token = $mfaToken;
        $user->mfa_token_expires_at = now()->addMinutes(10);
        $user->save();

        // Send MFA token via email
        Mail::raw("Your MFA token is: {$mfaToken}", function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Your MFA Token');
        });

        return response()->json(['message' => 'MFA token sent to your email']);
    }

    public function verifyMfa(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'mfa_token' => 'required|string|size:6'
        ]);

        $user = User::where('email', $request->email)
                    ->where('mfa_token', $request->mfa_token)
                    ->where('mfa_token_expires_at', '>', now())
                    ->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid or expired MFA token'], 401);
        }

        // Clear MFA token
        $user->mfa_token = null;
        $user->mfa_token_expires_at = null;
        $user->save();

        // Generate OAuth token
        $token = $user->createToken('Customer Portal')->accessToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 3600
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(['message' => 'Successfully logged out']);
    }
}
