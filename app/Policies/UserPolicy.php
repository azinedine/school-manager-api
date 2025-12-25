<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    public function view(User $user, User $model): bool
    {
        return $user->id === $model->id || $user->isAdmin() || $user->isManager();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    public function update(User $user, User $model): bool
    {
        if ($user->id === $model->id) {
            return true;
        }

        if ($user->isAdmin()) {
            return !$model->isSuperAdmin() && !$model->isAdmin();
        }

        if ($user->isManager()) {
            return !$model->isSuperAdmin() && !$model->isAdmin() && !$model->isManager();
        }

        return false;
    }

    public function delete(User $user, User $model): bool
    {
        // Users cannot delete themselves
        if ($user->id === $model->id) {
            return false;
        }

        if ($user->isAdmin()) {
            return !$model->isSuperAdmin() && !$model->isAdmin();
        }

        if ($user->isManager()) {
            return !$model->isSuperAdmin() && !$model->isAdmin() && !$model->isManager();
        }

        return false;
    }

    public function restore(User $user, User $model): bool
    {
        return false;
    }

    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }
}
