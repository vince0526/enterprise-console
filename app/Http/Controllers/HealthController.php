<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class HealthController
{
    public function __invoke(): JsonResponse
    {
        $status = [
            'ok' => true,
            'time' => now()->toIso8601String(),
            'db' => false,
            'cache' => false,
        ];

        // DB check
        try {
            DB::select('select 1 as ok');
            $status['db'] = true;
        } catch (\Throwable $e) {
            $status['ok'] = false;
            $status['db_error'] = substr($e->getMessage(), 0, 200);
        }

        // Cache check
        try {
            Cache::put('__health', '1', now()->addSeconds(10));
            $status['cache'] = Cache::get('__health') === '1';
            if (! $status['cache']) {
                $status['ok'] = false;
            }
        } catch (\Throwable $e) {
            $status['ok'] = false;
            $status['cache_error'] = substr($e->getMessage(), 0, 200);
        }

        return response()->json($status, $status['ok'] ? 200 : 503);
    }
}
