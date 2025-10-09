<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\SavedView;
use App\Models\User;

/**
 * Policy for SavedView CRUD
 */
class SavedViewPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Any authenticated user can list their own saved views
    }

    public function create(User $user): bool
    {
        return true; // Allow creation (upsert) for auth users
    }

    public function delete(User $user, SavedView $savedView): bool
    {
        return $savedView->user_id === $user->id;
    }
}
