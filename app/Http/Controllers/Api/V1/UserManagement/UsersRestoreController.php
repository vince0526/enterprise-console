<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\UserManagement;

use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

final class UsersRestoreController
{
    public function __invoke(int $user): JsonResponse
    {
        $auth = auth()->user();
        abort_unless($auth?->hasRole('admin'), 403);

        $model = User::withTrashed()->findOrFail($user);
        $model->restore();

        return (new UserResource($model))->response();
    }
}
