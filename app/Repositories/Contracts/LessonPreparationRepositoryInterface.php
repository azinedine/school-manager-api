<?php

namespace App\Repositories\Contracts;

use App\Models\LessonPreparation;
use Illuminate\Pagination\Paginator;

interface LessonPreparationRepositoryInterface
{
    /**
     * Get all lesson preparations for a teacher
     */
    public function getByTeacher(int $teacherId, array $filters = []): array;

    /**
     * Get a single lesson preparation by ID
     */
    public function findById(int $id): ?LessonPreparation;

    /**
     * Create a new lesson preparation
     */
    public function create(array $data): LessonPreparation;

    /**
     * Update an existing lesson preparation
     */
    public function update(int $id, array $data): LessonPreparation;

    /**
     * Delete a lesson preparation
     */
    public function delete(int $id): bool;

    /**
     * Get lesson preparations filtered and paginated
     */
    public function getFiltered(int $teacherId, array $filters = [], int $perPage = 15): Paginator;

    /**
     * Get lesson preparations by status
     */
    public function getByStatus(int $teacherId, string $status): array;

    /**
     * Get lesson preparations by class
     */
    public function getByClass(int $teacherId, string $class): array;

    /**
     * Get lesson preparations by subject
     */
    public function getBySubject(int $teacherId, string $subject): array;
}
