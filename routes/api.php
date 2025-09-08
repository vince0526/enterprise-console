<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AdminOnlyController;
use App\Http\Controllers\Api\V1\Companies\CompanyController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\Database\RelationsController;
use App\Http\Controllers\Api\V1\Database\TablesController;
use App\Http\Controllers\Api\V1\Databases\DatabaseConnectionController;
use App\Http\Controllers\Api\V1\HealthController;
use App\Http\Controllers\Api\V1\Restrictions\CompanyUserRestrictionController;
use App\Http\Controllers\Api\V1\UserManagement\UserController;
// --- API v1 strict resource routes ---
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rules\Password;

Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    // User management
    Route::apiResource('users', UserController::class);

    // Companies
    Route::apiResource('companies', CompanyController::class);

    // Database connections (nested under companies, shallow)
    Route::apiResource('companies.database-connections', DatabaseConnectionController::class)->shallow();

    // Restrictions
    Route::apiResource('company-user-restrictions', CompanyUserRestrictionController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    // Admin and dashboard
    Route::middleware('role:admin')->get('/admin/only', AdminOnlyController::class);
    Route::middleware('permission:view dashboard')->get('/dashboard', DashboardController::class);

    // Database schema endpoints
    Route::get('/database/{database_connection}/tables', TablesController::class);
    Route::get('/database/{database_connection}/relations', RelationsController::class); // ?table=...
});

// Health check
Route::get('/health', fn () => ['ok' => true, 'time' => now()]);
Route::get('/v1/health', HealthController::class);

// Auth endpoints
Route::post('/register', function (Request $request) {
    $data = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
        'password' => ['required', 'confirmed', Password::min(8)],
    ]);
    $user = \App\Models\User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => Hash::make($data['password']),
    ]);
    $token = $user->createToken('api')->plainTextToken;

    return response()->json(['token' => $token, 'user' => $user], 201);
});

Route::post('/login', function (Request $request) {
    $data = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    $user = \App\Models\User::where('email', $data['email'])->first();

    if (! $user || ! Hash::check($data['password'], $user->password)) {
        return response()->json(['message' => 'Invalid credentials'], 422);
    }

    $token = $user->createToken('api')->plainTextToken;

    return response()->json(['token' => $token, 'user' => $user]);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
    $request->user()->currentAccessToken()?->delete();

    return response()->json(['ok' => true]);
});
