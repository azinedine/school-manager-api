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
     */
    public function view(User $user, Lesson $lesson): bool
    {
        // Owner can always view
        if ($user->id === $lesson->teacher_id) {
            return true;
        }

        // Admin/Manager can view lessons in their institution
        if (($user->isAdmin() || $user->isManager()) && 
            $user->institution_id === $lesson->institution_id) {
            return true;
        }

        return false;
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
     * Owner can delete their own lessons.
     * Admin/Manager can delete lessons in their institution.
     */
    public function delete(User $user, Lesson $lesson): bool
    {
        // Owner can always delete
        if ($user->id === $lesson->teacher_id) {
            return true;
        }

        // Admin/Manager can delete lessons in their institution
        if (($user->isAdmin() || $user->isManager()) && 
            $user->institution_id === $lesson->institution_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the lesson.
     */
    public function restore(User $user, Lesson $lesson): bool
    {
        // Owner can restore their own
        if ($user->id === $lesson->teacher_id) {
            return true;
        }

        // Admin/Manager can restore in their institution
        if (($user->isAdmin() || $user->isManager()) && 
            $user->institution_id === $lesson->institution_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the lesson.
     */
    public function forceDelete(User $user, Lesson $lesson): bool
    {
        // Only allow within same institution for admins
        if (($user->isAdmin() || $user->isManager()) && 
            $user->institution_id === $lesson->institution_id) {
            return true;
        }

        return false;
    }
}
