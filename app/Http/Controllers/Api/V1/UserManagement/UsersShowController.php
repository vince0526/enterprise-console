<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\UserManagement;

use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

final class UsersShowController
{
    public function __invoke(User $user): JsonResponse
    {
        $this->authorize('view', $user);

        return (new UserResource($user->load('roles')))->response();
    }

    private function authorize(string $ability, mixed $arguments = []): void
    {
        abort_unless(auth()->user()?->can($ability, $arguments), 403);
    }
}
