<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (! $request->user() || ! $request->user()->hasVerifiedEmail()) {
            if ($request->expectsJson()) {
                abort(403, 'Your email address is not verified.');
            }

            return redirect()->route('verification.notice');
        }

        $result = $next($request);

        return $result instanceof Response ? $result : $result;
    }
}
