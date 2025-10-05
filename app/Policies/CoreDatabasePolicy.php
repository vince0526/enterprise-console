<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\CoreDatabase;
use App\Models\User;

class CoreDatabasePolicy
{
    private function allowAll(): bool
    {
        return app()->environment('testing') || (bool) config('app.dev_auto_login', false);
    }

    public function viewAny(?User $user): bool
    {
        if ($this->allowAll()) {
            return true;
        }

        return (bool) ($user?->can('core.view') ?? false);
    }

    public function view(?User $user, CoreDatabase $db): bool
    {
        if ($this->allowAll()) {
            return true;
        }

        return (bool) ($user?->can('core.view') ?? false);
    }

    public function create(?User $user): bool
    {
        if ($this->allowAll()) {
            return true;
        }

        return (bool) ($user?->can('core.create') ?? false);
    }

    public function update(?User $user, CoreDatabase $db): bool
    {
        if ($this->allowAll()) {
            return true;
        }

        return (bool) ($user?->can('core.update') ?? false);
    }

    public function delete(?User $user, CoreDatabase $db): bool
    {
        if ($this->allowAll()) {
            return true;
        }

        return (bool) ($user?->can('core.delete') ?? false);
    }

    public function manageOwners(?User $user, CoreDatabase $db): bool
    {
        if ($this->allowAll()) {
            return true;
        }

        return (bool) ($user?->can('core.manage-owners') ?? false);
    }

    public function manageLifecycle(?User $user, CoreDatabase $db): bool
    {
        if ($this->allowAll()) {
            return true;
        }

        return (bool) ($user?->can('core.manage-lifecycle') ?? false);
    }

    public function manageLinks(?User $user, CoreDatabase $db): bool
    {
        if ($this->allowAll()) {
            return true;
        }

        return (bool) ($user?->can('core.manage-links') ?? false);
    }
}
