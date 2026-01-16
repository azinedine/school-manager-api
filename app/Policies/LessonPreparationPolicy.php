<?php

namespace App\Policies;

use App\Models\LessonPreparation;
use App\Models\User;

class LessonPreparationPolicy
{
    /**
     * Super admins can do everything
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any lesson preparations.
     * Only teachers can view lesson preparations.
     */
    public function viewAny(User $user): bool
    {
        return $user->isTeacher();
    }

    /**
     * Determine whether the user can view the lesson preparation.
     * Only the owner can view their lesson preparation.
     */
    public function view(User $user, LessonPreparation $preparation): bool
    {
        return $user->id === $preparation->teacher_id;
    }

    /**
     * Determine whether the user can create lesson preparations.
     * Only teachers can create lesson preparations.
     */
    public function create(User $user): bool
    {
        return $user->isTeacher();
    }

    /**
     * Determine whether the user can update the lesson preparation.
     * Only the owner can update their lesson preparation.
     */
    public function update(User $user, LessonPreparation $preparation): bool
    {
        return $user->id === $preparation->teacher_id;
    }

    /**
     * Determine whether the user can delete the lesson preparation.
     * Only the owner can delete their lesson preparation.
     */
    public function delete(User $user, LessonPreparation $preparation): bool
    {
        return $user->id === $preparation->teacher_id;
    }

    /**
     * Determine whether the user can restore the lesson preparation.
     * Only the owner can restore their lesson preparation.
     */
    public function restore(User $user, LessonPreparation $preparation): bool
    {
        return $user->id === $preparation->teacher_id;
    }

    /**
     * Determine whether the user can permanently delete the lesson preparation.
     * Only the owner can permanently delete their lesson preparation.
     */
    public function forceDelete(User $user, LessonPreparation $preparation): bool
    {
        return $user->id === $preparation->teacher_id;
    }
}
