<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\CoreDatabase;
use App\Models\User;

class CoreDatabasePolicy
{
    private function allowAll(): bool
    {
        // In local and testing environments, or when dev auto-login is enabled,
        // allow all policy checks to pass for smoother developer experience.
        return app()->environment(['local', 'testing']) || (bool) config('app.dev_auto_login', false);
    }

    public function viewAny(?User $user): bool
    {
        if ($this->allowAll()) {
            return true;
        }

        return (bool) ($user?->can('core.view'));
    }

    public function view(?User $user, CoreDatabase $db): bool
    {
        if ($this->allowAll()) {
            return true;
        }

        return (bool) ($user?->can('core.view'));
    }

    public function create(?User $user): bool
    {
        if ($this->allowAll()) {
            return true;
        }

        return (bool) ($user?->can('core.create'));
    }

    public function update(?User $user, CoreDatabase $db): bool
    {
        if ($this->allowAll()) {
            return true;
        }

        return (bool) ($user?->can('core.update'));
    }

    public function delete(?User $user, CoreDatabase $db): bool
    {
        if ($this->allowAll()) {
            return true;
        }

        return (bool) ($user?->can('core.delete'));
    }

    public function manageOwners(?User $user, CoreDatabase $db): bool
    {
        if ($this->allowAll()) {
            return true;
        }

        return (bool) ($user?->can('core.manage-owners'));
    }

    public function manageLifecycle(?User $user, CoreDatabase $db): bool
    {
        if ($this->allowAll()) {
            return true;
        }

        return (bool) ($user?->can('core.manage-lifecycle'));
    }

    public function manageLinks(?User $user, CoreDatabase $db): bool
    {
        if ($this->allowAll()) {
            return true;
        }

        return (bool) ($user?->can('core.manage-links'));
    }
}
