<?php

namespace App\Policies;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LessonPolicy
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
     * Determine whether the user can view any lessons.
     * Teachers can view their own lessons.
     * Admins can view all lessons in their institution.
     */
    public function viewAny(User $user): bool
    {
        return $user->isTeacher() || $user->isAdmin() || $user->isManager();
    }

    /**
     * Determine whether the user can view the lesson.
     * Only the owner can view their lesson.
     */
    public function view(User $user, Lesson $lesson): bool
    {
        // Only owner can view
        return $user->id === $lesson->teacher_id;
    }

    /**
     * Determine whether the user can create lessons.
     * Only teachers can create lessons.
     */
    public function create(User $user): bool
    {
        return $user->isTeacher();
    }

    /**
     * Determine whether the user can update the lesson.
     * Only the owner can update their lessons.
     */
    public function update(User $user, Lesson $lesson): bool
    {
        return $user->id === $lesson->teacher_id;
    }

    /**
     * Determine whether the user can delete the lesson.
     * Only the owner can delete their lesson.
     */
    public function delete(User $user, Lesson $lesson): bool
    {
        // Only owner can delete
        return $user->id === $lesson->teacher_id;
    }

    /**
     * Determine whether the user can restore the lesson.
     * Only the owner can restore their lesson.
     */
    public function restore(User $user, Lesson $lesson): bool
    {
        // Only owner can restore
        return $user->id === $lesson->teacher_id;
    }

    /**
     * Determine whether the user can permanently delete the lesson.
     * Only super admin can force delete.
     */
    public function forceDelete(User $user, Lesson $lesson): bool
    {
        // Only super admin via before() method
        return false;
    }
}
