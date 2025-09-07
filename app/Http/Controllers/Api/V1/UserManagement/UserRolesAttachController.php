<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\UserManagement;

use App\Http\Requests\Api\V1\UserManagement\RoleAttachRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

final class UserRolesAttachController
{
    public function __invoke(RoleAttachRequest $request, User $user): JsonResponse
    {
        $roles = (array) $request->validated()['roles'];
        $user->syncRoles(array_values($roles)); // replace full set

        return (new UserResource($user->load('roles')))->response();
    }
}
