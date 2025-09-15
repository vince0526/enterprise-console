<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DevOverrideUsed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly int $userId, public readonly string $email, public readonly ?string $ip = null) {}

    public static function fromUser(User $user, ?string $ip = null): self
    {
        return new self($user->getKey(), (string) $user->email, $ip);
    }
}
