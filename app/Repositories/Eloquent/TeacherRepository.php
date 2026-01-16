<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\TeacherRepositoryInterface;

class TeacherRepository implements TeacherRepositoryInterface
{
    /**
     * Get all teachers with pagination.
     *
     * @param  int  $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAllTeachers($perPage = 20)
    {
        return User::where('role', 'teacher')
            ->with(['institution', 'wilaya', 'municipality']) // Eager load relationships
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
