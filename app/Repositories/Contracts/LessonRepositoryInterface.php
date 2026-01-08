<?php

namespace App\Repositories\Contracts;

use App\Models\Lesson;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface LessonRepositoryInterface
{
    /**
     * Get all lessons for a teacher with optional filters
     *
     * @param int $teacherId
     * @param array $filters
     * @return array
     */
    public function getByTeacher(int $teacherId, array $filters = []): array;

    /**
     * Get all lessons for an institution with optional filters
     *
     * @param int $institutionId
     * @param array $filters
     * @return array
     */
    public function getByInstitution(int $institutionId, array $filters = []): array;

    /**
     * Find a lesson by ID
     *
     * @param int $id
     * @return Lesson|null
     */
    public function findById(int $id): ?Lesson;

    /**
     * Find a lesson by ID scoped to institution
     *
     * @param int $id
     * @param int $institutionId
     * @return Lesson|null
     */
    public function findByIdInInstitution(int $id, int $institutionId): ?Lesson;

    /**
     * Create a new lesson
     *
     * @param array $data
     * @return Lesson
     */
    public function create(array $data): Lesson;

    /**
     * Update an existing lesson
     *
     * @param int $id
     * @param array $data
     * @return Lesson
     */
    public function update(int $id, array $data): Lesson;

    /**
     * Delete a lesson (soft delete)
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Get filtered and paginated lessons for a teacher
     *
     * @param int $teacherId
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getFiltered(int $teacherId, array $filters = [], int $perPage = 15): LengthAwarePaginator;
}
