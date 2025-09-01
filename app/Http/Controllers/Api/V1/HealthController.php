<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HealthController extends Controller
{
    public function __invoke()
    {
        $checks = [
            'app' => true,
            'db' => $this->dbOk(),
            'cache' => $this->cacheOk(),
        ];

        return response()->json([
            'ok' => ! in_array(false, $checks, true),
            'checks' => $checks,
        ]);
    }

    private function dbOk(): bool
    {
        try {
            DB::select('select 1');

            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    private function cacheOk(): bool
    {
        try {
            Cache::put('_health', '1', 5);

            return Cache::get('_health') === '1';
        } catch (\Throwable) {
            return false;
        }
    }
}
