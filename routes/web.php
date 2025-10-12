<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\DevOverrideController;
use App\Http\Controllers\Auth\ForgotUsernameController;
use App\Http\Controllers\Auth\OAuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Web\Database\CompanyUserController as WebCompanyUser;
use App\Http\Controllers\Web\Database\UserRestrictionController as WebUserRestriction;
use App\Http\Controllers\Web\Emc\CoreDatabaseController;
use App\Http\Controllers\Web\Emc\CoreDatabaseLifecycleEventController;
use App\Http\Controllers\Web\Emc\CoreDatabaseLinkController;
use App\Http\Controllers\Web\Emc\CoreDatabaseOwnerController;
use App\Http\Controllers\Web\Emc\EmcController;
use App\Http\Middleware\EnsureDevOverrideEnabled;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Lightweight health check endpoint for readiness/liveness probes
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'app' => config('app.name'),
        'env' => config('app.env'),
        'time' => now()->toIso8601String(),
    ]);
})->name('health');

// Make Core Databases the default entry across all environments
Route::get('/', fn () => redirect('/emc/core'));
// Lightweight read-only endpoints for ERD taxonomies/programs (temporary for verification)
Route::prefix('erd')->middleware('throttle:120,1')->group(function () {
    Route::get('industries', [\App\Http\Controllers\Web\Erd\ErdBrowseController::class, 'industries']);
    Route::get('subindustries', [\App\Http\Controllers\Web\Erd\ErdBrowseController::class, 'subindustries']);
    Route::get('stages', [\App\Http\Controllers\Web\Erd\ErdBrowseController::class, 'stages']);
    Route::get('public-goods', [\App\Http\Controllers\Web\Erd\ErdBrowseController::class, 'publicGoods']);
    Route::get('programs', [\App\Http\Controllers\Web\Erd\ErdBrowseController::class, 'programs']);
    Route::get('gov-orgs', [\App\Http\Controllers\Web\Erd\ErdBrowseController::class, 'govOrgs']);
    Route::get('cso-super-categories', [\App\Http\Controllers\Web\Erd\ErdBrowseController::class, 'csoSuperCategories']);
    Route::get('cso-types', [\App\Http\Controllers\Web\Erd\ErdBrowseController::class, 'csoTypes']);
});

// If dev auto login is enabled, expose dashboard without auth middleware.
if (config('app.dev_auto_login', false)) {
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');
} else {
    Route::get('/dashboard', fn () => view('dashboard'))
        ->middleware(['auth', 'verified'])
        ->name('dashboard');
}

Route::middleware(config('app.dev_auto_login', false) ? [] : ['auth'])->group(function () {
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

Route::post('/dev-override', DevOverrideController::class)
    ->middleware([EnsureDevOverrideEnabled::class, 'throttle:5,1'])
    ->name('dev.override');

Route::post('/auth/recover-username', [ForgotUsernameController::class, 'send'])->name('auth.recover-username');
Route::post('/auth/verify-username-code', [ForgotUsernameController::class, 'verify'])->name('auth.verify-username-code');
Route::get('/oauth/redirect/{provider}', [OAuthController::class, 'redirect'])->name('oauth.redirect');
Route::get('/oauth/callback/{provider}', [OAuthController::class, 'callback'])->name('oauth.callback');

// TEMP: Dev flag inspection route (remove after verification)
if (! app()->environment('production')) {
    Route::get('/dev-env-flag', function () {
        return response()->json([
            'DEV_AUTO_LOGIN' => config('app.dev_auto_login', false),
            'DEV_AUTO_LOGIN_USER_ID' => config('app.dev_auto_login_user_id', 1),
            'auth_user_id' => Auth::id(),
            'is_authenticated' => Auth::check(),
        ])->header('X-Dev-Auto-Login', config('app.dev_auto_login', false) ? '1' : '0');
    });

    Route::get('/dev-users', function () {
        $users = \App\Models\User::query()->orderBy('id')->limit(5)->get(['id', 'name', 'email', 'email_verified_at'])->toArray();

        return response()->json([
            'count' => \App\Models\User::query()->count(),
            'sample' => $users,
        ]);
    });

    Route::get('/dev', function () {
        return view('dev.nav');
    });

    Route::get('/dev-plain', function () {
        return response('<!DOCTYPE html><html><head><meta charset="utf-8"/><title>Dev Links</title><style>body{background:#111;color:#eee;font-family:system-ui,Arial,sans-serif;padding:2rem;} a{color:#7db3ff;text-decoration:none;} a:hover{text-decoration:underline;} h1{margin-top:0;} code{background:#222;padding:2px 4px;border-radius:4px;} .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:1rem;} .card{border:1px solid #333;padding:1rem;border-radius:8px;background:#1a1a1a;} ul{margin:0;padding-left:1.1rem;} li{margin:.25rem 0;} .warn{margin-top:2rem;font-size:.8rem;opacity:.75}</style></head><body><h1>Dev Links (Plain)</h1><p>Auth bypass flag: <strong>'.(config('app.dev_auto_login', false) ? 'ON' : 'OFF').'</strong></p><div class="grid">
<div class="card"><h2>Core</h2><ul><li><a href="/">Welcome</a></li><li><a href="/dashboard">Dashboard</a></li><li><a href="/profile">Profile Edit</a></li></ul></div>
<div class="card"><h2>Database</h2><ul><li><a href="/database/company-users">Company Users</a></li><li><a href="/database/user-restrictions">User Restrictions</a></li></ul></div>
<div class="card"><h2>Auth</h2><ul><li><a href="/login">Login</a></li><li><a href="/register">Register</a></li><li><a href="/forgot-password">Forgot Password</a></li></ul></div>
</div><p class="warn">Remove route in <code>routes/web.php</code> before committing.</p></body></html>');
    });

    // keep dev helper routes only in non-production
}

// EMC prototype routes (model navigation) - always available
Route::prefix('emc')->name('emc.')->group(function () {
    // Serve model HTMLs directly
    Route::get('/model-html', function () {
        $path = base_path('docs/enterprise_management_console.model.html');
        abort_unless(file_exists($path), 404);

        return response()->file($path, [
            'Content-Type' => 'text/html; charset=utf-8',
        ]);
    })->name('model-html');

    Route::get('/layout-html', function () {
        $path = base_path('docs/enterprise_management_console.layout.html');
        abort_unless(file_exists($path), 404);

        return response()->file($path, [
            'Content-Type' => 'text/html; charset=utf-8',
        ]);
    })->name('layout-html');

    Route::get('/', function () {
        return redirect('/emc/core');
    })->name('index');
    // Core Databases module (placed before Database Management in nav)
    Route::prefix('core')->name('core.')->group(function () {
        Route::get('/', [CoreDatabaseController::class, 'index'])->name('index');
        Route::post('/', [CoreDatabaseController::class, 'store'])->name('store');
        Route::patch('/{core_database}', [CoreDatabaseController::class, 'update'])->name('update');
        Route::delete('/{core_database}', [CoreDatabaseController::class, 'destroy'])->name('destroy');

        // Tools
        Route::get('/export/csv', [CoreDatabaseController::class, 'exportCsv'])->name('export.csv');
        Route::post('/ddl', [CoreDatabaseController::class, 'generateDdl'])->name('ddl');

        Route::post('owners', [CoreDatabaseOwnerController::class, 'store'])->name('owners.store');
        Route::delete('owners/{owner}', [CoreDatabaseOwnerController::class, 'destroy'])->name('owners.destroy');

        Route::post('lifecycle-events', [CoreDatabaseLifecycleEventController::class, 'store'])->name('lifecycle-events.store');
        Route::delete('lifecycle-events/{event}', [CoreDatabaseLifecycleEventController::class, 'destroy'])->name('lifecycle-events.destroy');

        Route::post('links', [CoreDatabaseLinkController::class, 'store'])->name('links.store');
        Route::delete('links/{link}', [CoreDatabaseLinkController::class, 'destroy'])->name('links.destroy');

        // Saved Views JSON API
        Route::middleware('throttle:60,1')->group(function () {
            Route::get('saved-views', [\App\Http\Controllers\Web\Emc\SavedViewController::class, 'index'])->name('saved-views.index');
            Route::post('saved-views', [\App\Http\Controllers\Web\Emc\SavedViewController::class, 'store'])->name('saved-views.store');
            Route::delete('saved-views/{savedView}', [\App\Http\Controllers\Web\Emc\SavedViewController::class, 'destroy'])->name('saved-views.destroy');
            Route::patch('saved-views/{savedView}', [\App\Http\Controllers\Web\Emc\SavedViewController::class, 'update'])->name('saved-views.update');
            Route::post('saved-views/{savedView}/duplicate', [\App\Http\Controllers\Web\Emc\SavedViewController::class, 'duplicate'])->name('saved-views.duplicate');
        });
    });

    Route::get('/db', [EmcController::class, 'db'])->name('db');
    Route::get('/tables', [EmcController::class, 'tables'])->name('tables');
    Route::get('/files', [EmcController::class, 'files'])->name('files');
    Route::get('/users', [EmcController::class, 'users'])->name('users');
    Route::get('/reports', [EmcController::class, 'reports'])->name('reports');
    Route::get('/ai', [EmcController::class, 'ai'])->name('ai');
    Route::get('/comms', [EmcController::class, 'comms'])->name('comms');
    Route::get('/settings', [EmcController::class, 'settings'])->name('settings');
    Route::get('/activity', [EmcController::class, 'activity'])->name('activity');
    Route::get('/about', [EmcController::class, 'about'])->name('about');
    Route::get('/tables/{table}/filters', [EmcController::class, 'filters'])->name('filters');
});

// Tools: Core Databases Workbench (React mount)
Route::get('/tools/core-workbench', function () {
    return view('tools.workbench');
})->name('tools.core-workbench');

// CI smoke trigger: non-functional change to verify workflow runs on code path updates
