<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Mail\SendVerificationCode; // <-- Import
use Illuminate\Support\Facades\Mail;  // <-- Import
use Illuminate\Support\Facades\Hash; // <-- Import (though model handles it)

class AuthController extends Controller
{
    /**
     * Handle user registration.
     */
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        // 1. Generate 6-digit code
        $verificationCode = strval(rand(100000, 999999));

        // 2. Create the user
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => $validatedData['password'], // Auto-hashed by your model's cast
            'verification_code' => $verificationCode,
            'code_expires_at' => now()->addMinutes(10)
        ]);

        // 3. Send the verification email using Gmail
        try {
            Mail::to($user->email)->send(new SendVerificationCode($verificationCode));
        } catch (\Exception $e) {
            // Log the error
            report($e);
            return response()->json([
                'message' => 'User created, but failed to send verification email. Check server logs.'
            ], 500);
        }

        // 4. Respond to the Vue frontend
        // This tells Vue to show the "Verify Code" screen
        return response()->json([
            'message' => 'Registration successful! Please check your email for a 6-digit code.',
            'email' => $user->email // Send back email for the Vue app to use
        ], 201);
    }

    /**
     * Handle email verification from the Vue app.
     */
    public function verifyEmail(Request $request)
{
    // 1. Validate the request
    $validatedData = $request->validate([
        'email' => 'required|string|email',
        'code' => 'required|string|min:6|max:6',
    ]);

    // 2. Find the user
    $user = User::where('email', $validatedData['email'])->first();

    if (!$user) {
        return response()->json(['message' => 'User not found.'], 404);
    }

    // 3. (NEW FIX) Check for a valid, pending code FIRST
    // If either of these is null, the user has no code to verify.
    if (is_null($user->verification_code) || is_null($user->code_expires_at)) {
        return response()->json([
            'message' => 'No active verification code. Please log in again to receive one.'
        ], 400);
    }

    // 4. Check if the code has expired (This is now safe to run)
    if (now()->isAfter($user->code_expires_at)) {
        return response()->json(['message' => 'Your verification code has expired. Please log in again.'], 400);
    }

    // 5. Check if the code is correct
    if ($user->verification_code !== $validatedData['code']) {
        return response()->json(['message' => 'Invalid verification code.'], 400);
    }

    // 6. If everything is correct, verify the user
    $user->email_verified_at = now();
    $user->verification_code = null; // Clear the code
    $user->code_expires_at = null;
    $user->save();

    // 7. Create and return an auth token
    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
        'message' => 'Email verified successfully! You are now logged in.',
        'user' => $user,
        'token' => $token
    ]);
}
    /**
     * Handle user login.
     * Validates credentials and sends a 2FA code.
     */
    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validatedData['email'])->first();

        // 1. Check if user exists and password is correct
        if (!$user || !Hash::check($validatedData['password'], $user->password)) {
            return response()->json(['message' => 'Invalid email or password.'], 401);
        }

        // 2. Generate a new code
        $verificationCode = strval(rand(100000, 999999));

        // 3. Save code and expiry to the user
        $user->verification_code = $verificationCode;
        $user->code_expires_at = now()->addMinutes(10);
        $user->save();

        // 4. Send the email
        try {
            Mail::to($user->email)->send(new SendVerificationCode($verificationCode));
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'message' => 'Login successful, but failed to send verification email.'
            ], 500);
        }

        // 5. Respond to Vue
        return response()->json([
            'message' => 'Login successful! Please check your email for your 2FA code.',
            'email' => $user->email
        ]);
    }

    /**
     * Resend the verification code.
     */
    public function resendCode(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|string|email',
        ]);

        $user = User::where('email', $validatedData['email'])->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // Generate and save a new code
        $verificationCode = strval(rand(100000, 999999));
        $user->verification_code = $verificationCode;
        $user->code_expires_at = now()->addMinutes(10);
        $user->save();

        // Send the email
        try {
            Mail::to($user->email)->send(new SendVerificationCode($verificationCode));
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'message' => 'Failed to resend verification email.'
            ], 500);
        }

        return response()->json([
            'message' => 'A new verification code has been sent to your email.'
        ]);
    }
}

