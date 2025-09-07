<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\UserManagement;

use App\Http\Requests\Api\V1\UserManagement\UserUpdateRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

final class UsersUpdateController
{
    public function __invoke(UserUpdateRequest $request, User $user): JsonResponse
    {
        $data = $request->validated();

        if (array_key_exists('password', $data)) {
            $data['password'] = Hash::make((string) $data['password']);
        }

        $user->fill($data)->save();

        return (new UserResource($user))->response();
    }
}
