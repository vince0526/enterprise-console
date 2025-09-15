<?php

declare(strict_types=1);

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Web\Database\CompanyUserController as WebCompanyUser;
use App\Http\Controllers\Web\Database\UserRestrictionController as WebUserRestriction;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'));

Route::get('/dashboard', fn () => view('dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('database/company-users', WebCompanyUser::class)
        ->parameters(['company-users' => 'company_user'])
        ->names('company-users');

    Route::resource('database/user-restrictions', WebUserRestriction::class)
        ->parameters(['user-restrictions' => 'user_restriction'])
        ->names('user-restrictions');
});

require __DIR__.'/auth.php';

use App\Http\Controllers\Auth\DevOverrideController;
use App\Http\Controllers\Auth\ForgotUsernameController;
use App\Http\Controllers\Auth\OAuthController;

Route::post('/dev-override', DevOverrideController::class)
    ->middleware('dev.override')
    ->name('dev.override');

Route::post('/auth/recover-username', [ForgotUsernameController::class, 'send'])->name('auth.recover-username');
Route::post('/auth/verify-username-code', [ForgotUsernameController::class, 'verify'])->name('auth.verify-username-code');
Route::get('/oauth/redirect/{provider}', [OAuthController::class, 'redirect'])->name('oauth.redirect');
Route::get('/oauth/callback/{provider}', [OAuthController::class, 'callback'])->name('oauth.callback');
