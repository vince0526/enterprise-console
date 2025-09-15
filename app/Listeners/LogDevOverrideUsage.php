<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\DevOverrideUsed;
use App\Models\DevOverrideLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class LogDevOverrideUsage
{
    public function handle(DevOverrideUsed $event): void
    {
        Log::info('LogDevOverrideUsage listener handling event', ['email' => $event->email]);
        DevOverrideLog::create([
            'user_id' => $event->userId,
            'email' => $event->email,
            'ip' => $event->ip ?? Request::ip(),
        ]);
    }
}
