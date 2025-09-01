<?php

use App\Http\Controllers\Api\V1\AdminOnlyController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\HealthController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rules\Password;

Route::prefix('v1')->group(function () {
    Route::get('/health', HealthController::class);

    Route::middleware('auth:sanctum')->group(function () {
        Route::middleware('role:admin')->get('/admin/only', AdminOnlyController::class);
        Route::middleware('permission:view dashboard')->get('/dashboard', DashboardController::class);
    });
});

Route::get('/health', fn () => ['ok' => true, 'time' => now()]);

Route::post('/register', function (Request $request) {
    $data = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
        'password' => ['required', 'confirmed', Password::min(8)],
    ]);
    $user = User::create([
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

    $user = User::where('email', $data['email'])->first();

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

Route::middleware(['auth:sanctum', 'role:admin'])->get('/admin/only', fn () => ['ok' => true, 'scope' => 'admin']);
Route::middleware(['auth:sanctum', 'permission:view dashboard'])->get('/dashboard', fn () => ['ok' => true]);

Route::middleware(['auth:sanctum', 'role:admin'])->get('/admin/only', fn () => [
    'ok' => true,
    'scope' => 'admin',
]);

Route::middleware(['auth:sanctum', 'permission:view dashboard'])->get('/dashboard', fn () => [
    'ok' => true,
    'feature' => 'dashboard',
]);
