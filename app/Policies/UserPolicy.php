<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

final class UserPolicy
{
    public function viewAny(User $auth): bool
    {
        return $auth->hasRole('admin');
    }

    public function view(User $auth, User $user): bool
    {
        return $auth->hasRole('admin') || $auth->id === $user->id;
    }

    public function create(User $auth): bool
    {
        return $auth->hasRole('admin');
    }

    public function update(User $auth, User $user): bool
    {
        return $auth->hasRole('admin') || $auth->id === $user->id;
    }

    public function delete(User $auth, User $user): bool
    {
        return $auth->hasRole('admin') && $auth->id !== $user->id;
    }

    public function restore(User $auth, User $user): bool
    {
        return $auth->hasRole('admin');
    }

    public function forceDelete(User $auth, User $user): bool
    {
        return false;
    }
}
