<?php

namespace App\Repositories\Contracts;

use App\Models\Lesson;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface LessonRepositoryInterface
{
    /**
     * Get all lessons for a teacher with optional filters
     */
    public function getByTeacher(int $teacherId, array $filters = []): array;

    /**
     * Get all lessons for an institution with optional filters
     */
    public function getByInstitution(int $institutionId, array $filters = []): array;

    /**
     * Find a lesson by ID
     */
    public function findById(int $id): ?Lesson;

    /**
     * Find a lesson by ID scoped to institution
     */
    public function findByIdInInstitution(int $id, int $institutionId): ?Lesson;

    /**
     * Create a new lesson
     */
    public function create(array $data): Lesson;

    /**
     * Update an existing lesson
     */
    public function update(int $id, array $data): Lesson;

    /**
     * Delete a lesson (soft delete)
     */
    public function delete(int $id): bool;

    /**
     * Get filtered and paginated lessons for a teacher
     */
    public function getFiltered(int $teacherId, array $filters = [], int $perPage = 15): LengthAwarePaginator;
}
