<?php

namespace App\Repositories\Contracts;

interface TeacherRepositoryInterface
{
    /**
     * Get all teachers with pagination.
     *
     * @param  int  $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAllTeachers($perPage = 20);
}
