<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\UserManagement;

use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

final class UsersIndexController
{
    public function __invoke(): JsonResponse
    {
        $this->authorize('viewAny', User::class);

        $users = User::query()->with('roles')->paginate(perPage: 15);

        return UserResource::collection($users)->response();
    }

    private function authorize(string $ability, mixed $arguments = []): void
    {
        abort_unless((bool) auth()->user()?->can($ability, $arguments), 403);
    }
}
