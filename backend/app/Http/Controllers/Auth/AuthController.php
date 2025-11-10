<?php
namespace App\Http\Controllers\Auth;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(Request $request)
{
    // 1. Validate the incoming data from the form
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8', // You can add confirmation rules too
    ]);

    // 2. Create the user
    // This works because 'name', 'email', and 'password' are in your $fillable array
    $user = User::create([
        'name' => $validatedData['name'],
        'email' => $validatedData['email'],
        'password' => $validatedData['password'], // Laravel will auto-hash this!
    ]);

    // 3. User is created in your 'users' collection in MongoDB
    // You can now log them in, return a token, etc.

    return response()->json([
        'message' => 'User created successfully',
        'user' => $user
    ], 201);
}
}
