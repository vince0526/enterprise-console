<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\UserManagement;

use App\Http\Requests\Api\V1\UserManagement\PermissionGrantRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

final class UserPermissionsGrantController
{
    public function __invoke(PermissionGrantRequest $request, User $user): JsonResponse
    {
        $perms = (array) $request->validated()['permissions'];
        $user->syncPermissions(array_values($perms));

        return (new UserResource($user->load('roles')))->response();
    }
}
