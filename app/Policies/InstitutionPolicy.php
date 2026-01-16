<?php

namespace App\Policies;

use App\Models\Institution;
use App\Models\User;

class InstitutionPolicy
{
    /**
     * Determine whether the user can view any institutions.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view institutions list
        return true;
    }

    /**
     * Determine whether the user can view the institution.
     */
    public function view(User $user, Institution $institution): bool
    {
        // All authenticated users can view an institution
        return true;
    }

    /**
     * Determine whether the user can create institutions.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'admin']);
    }

    /**
     * Determine whether the user can update the institution.
     */
    public function update(User $user, Institution $institution): bool
    {
        if (in_array($user->role, ['super_admin'])) {
            return true;
        }

        // Admin can only update their own institution
        if ($user->role === 'admin' && $user->institution_id === $institution->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the institution.
     */
    public function delete(User $user, Institution $institution): bool
    {
        // Only super_admin can delete institutions
        return $user->role === 'super_admin';
    }

    /**
     * Determine whether the user can restore the institution.
     */
    public function restore(User $user, Institution $institution): bool
    {
        return $user->role === 'super_admin';
    }

    /**
     * Determine whether the user can permanently delete the institution.
     */
    public function forceDelete(User $user, Institution $institution): bool
    {
        return $user->role === 'super_admin';
    }
}
