<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\UserManagement;

use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

final class UserRolesDetachController
{
    public function __invoke(User $user): JsonResponse
    {
        abort_unless((bool) auth()->user()?->hasRole('admin'), 403);
        $user->syncRoles([]);

        return (new UserResource($user->load('roles')))->response();
    }
}
