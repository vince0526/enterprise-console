<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDevOverrideEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment('production') || ! (bool) config('dev_override.enabled')) {
            return response()->json([
                'success' => false,
                'message' => 'dev override disabled',
            ], 403);
        }

        if (! config('dev_override.token')) {
            return response()->json([
                'success' => false,
                'message' => 'dev override token not configured',
            ], 500);
        }

        return $next($request);
    }
}
