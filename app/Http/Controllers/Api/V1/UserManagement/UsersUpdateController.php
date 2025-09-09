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
        /** @var array<string, string> $data */
        $data = $request->validated();

        if (array_key_exists('password', $data)) {
            // inside this branch the key exists and is a string per the FormRequest
            $data['password'] = Hash::make($data['password']);
        }

        $user->fill($data)->save();

        return (new UserResource($user))->response();
    }
}
