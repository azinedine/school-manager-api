<?php

namespace App\Repositories;

use App\Models\LessonPreparation;
use App\Repositories\Contracts\LessonPreparationRepositoryInterface;
use Illuminate\Pagination\Paginator;

class LessonPreparationRepository implements LessonPreparationRepositoryInterface
{
    /**
     * Get all lesson preparations for a teacher
     */
    public function getByTeacher(int $teacherId, array $filters = []): array
    {
        $query = LessonPreparation::byTeacher($teacherId);

        if (isset($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        if (isset($filters['class'])) {
            $query->byClass($filters['class']);
        }

        if (isset($filters['subject'])) {
            $query->bySubject($filters['subject']);
        }

        return $query->orderByDate()->get()->toArray();
    }

    /**
     * Get a single lesson preparation by ID
     */
    public function findById(int $id): ?LessonPreparation
    {
        return LessonPreparation::find($id);
    }

    /**
     * Create a new lesson preparation
     */
    public function create(array $data): LessonPreparation
    {
        return LessonPreparation::create($data);
    }

    /**
     * Update an existing lesson preparation
     */
    public function update(int $id, array $data): LessonPreparation
    {
        $preparation = $this->findById($id);

        if (!$preparation) {
            throw new \Exception("Lesson preparation with ID {$id} not found");
        }

        $preparation->update($data);

        return $preparation;
    }

    /**
     * Delete a lesson preparation
     */
    public function delete(int $id): bool
    {
        $preparation = $this->findById($id);

        if (!$preparation) {
            return false;
        }

        return $preparation->delete();
    }

    /**
     * Get lesson preparations filtered and paginated
     */
    public function getFiltered(int $teacherId, array $filters = [], int $perPage = 15): Paginator
    {
        $query = LessonPreparation::byTeacher($teacherId);

        if (isset($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        if (isset($filters['class'])) {
            $query->byClass($filters['class']);
        }

        if (isset($filters['subject'])) {
            $query->bySubject($filters['subject']);
        }

        return $query->orderByDate()->simplePaginate($perPage);
    }

    /**
     * Get lesson preparations by status
     */
    public function getByStatus(int $teacherId, string $status): array
    {
        return LessonPreparation::byTeacher($teacherId)
            ->byStatus($status)
            ->orderByDate()
            ->get()
            ->toArray();
    }

    /**
     * Get lesson preparations by class
     */
    public function getByClass(int $teacherId, string $class): array
    {
        return LessonPreparation::byTeacher($teacherId)
            ->byClass($class)
            ->orderByDate()
            ->get()
            ->toArray();
    }

    /**
     * Get lesson preparations by subject
     */
    public function getBySubject(int $teacherId, string $subject): array
    {
        return LessonPreparation::byTeacher($teacherId)
            ->bySubject($subject)
            ->orderByDate()
            ->get()
            ->toArray();
    }
}
