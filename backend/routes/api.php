<?php

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
