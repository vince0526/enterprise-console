<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\UserManagement;

use App\Http\Requests\Api\V1\UserManagement\UserStoreRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

final class UsersStoreController
{
    public function __invoke(UserStoreRequest $request): JsonResponse
    {
        /** @var array<string, mixed> $data */
        $data = $request->validated();

        $nameRaw = $data['name'] ?? null;
        $emailRaw = $data['email'] ?? null;
        $passwordRaw = $data['password'] ?? null;

        $name = is_scalar($nameRaw) ? (string) $nameRaw : '';
        $email = is_scalar($emailRaw) ? (string) $emailRaw : '';
        $password = is_scalar($passwordRaw) ? (string) $passwordRaw : '';

        $user = User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        return (new UserResource($user))->response()->setStatusCode(201);
    }
}
