<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\UserManagement;

use App\Models\User;
use Illuminate\Http\JsonResponse;

final class UsersDestroyController
{
    public function __invoke(User $user): JsonResponse
    {
        abort_unless((bool) auth()->user()?->can('delete', $user), 403);
        $user->delete();

        return response()->json(['deleted' => true]);
    }
}
