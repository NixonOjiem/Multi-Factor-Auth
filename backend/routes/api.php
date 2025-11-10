<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/**
 * --------------------------------
 * Unprotected (Public) Routes
 * --------------------------------
 *
 * Routes accessible to anyone without authentication.
 */

// A simple public endpoint to check if the API is running
Route::get('/status', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'API is up and running!'
    ]);
});

/**
 * --------------------------------
 * Unprotected (Public) Routes
 * --------------------------------
 *
 * My Auth Routes
 */
// verification route
Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
// Used For user registration
Route::post('/register', [AuthController::class, 'register']);
// Used for login route
Route::post('/login', [AuthController::class, 'login']);
// used to send verification code again
Route::post('/resend-code', [AuthController::class, 'resendCode']);


/**
 * --------------------------------
 * Protected (Authenticated) Routes
 * --------------------------------
 *
 * These routes require authentication via Sanctum (or Passport).
 * The 'auth:sanctum' middleware will protect them.
 */

Route::middleware('auth:sanctum')->group(function () {

    // The most common test: returns the currently authenticated user
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // You could add other protected routes here, like:
    // Route::get('/my-data', [MyDataController::class, 'index']);

});
