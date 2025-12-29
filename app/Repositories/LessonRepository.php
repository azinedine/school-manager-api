<?php

namespace App\Repositories;

use App\Models\Lesson;
use App\Repositories\Contracts\LessonRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LessonRepository implements LessonRepositoryInterface
{
    /**
     * Get all lessons for a teacher with optional filters
     */
    public function getByTeacher(int $teacherId, array $filters = []): array
    {
        $query = Lesson::byTeacher($teacherId)->with(['teacher', 'institution']);

        $this->applyFilters($query, $filters);

        return $query->orderByDate()->get()->toArray();
    }

    /**
     * Get all lessons for an institution with optional filters
     */
    public function getByInstitution(int $institutionId, array $filters = []): array
    {
        $query = Lesson::byInstitution($institutionId)->with(['teacher', 'institution']);

        $this->applyFilters($query, $filters);

        return $query->orderByDate()->get()->toArray();
    }

    /**
     * Find a lesson by ID
     */
    public function findById(int $id): ?Lesson
    {
        return Lesson::with(['teacher', 'institution'])->find($id);
    }

    /**
     * Find a lesson by ID scoped to institution
     */
    public function findByIdInInstitution(int $id, int $institutionId): ?Lesson
    {
        return Lesson::with(['teacher', 'institution'])
            ->where('id', $id)
            ->where('institution_id', $institutionId)
            ->first();
    }

    /**
     * Create a new lesson
     */
    public function create(array $data): Lesson
    {
        $lesson = Lesson::create($data);
        return $lesson->load(['teacher', 'institution']);
    }

    /**
     * Update an existing lesson
     */
    public function update(int $id, array $data): Lesson
    {
        $lesson = $this->findById($id);

        if (!$lesson) {
            throw new \Exception("Lesson with ID {$id} not found");
        }

        $lesson->update($data);

        return $lesson->fresh(['teacher', 'institution']);
    }

    /**
     * Delete a lesson (soft delete)
     */
    public function delete(int $id): bool
    {
        $lesson = $this->findById($id);

        if (!$lesson) {
            return false;
        }

        return $lesson->delete();
    }

    /**
     * Get filtered and paginated lessons for a teacher
     */
    public function getFiltered(int $teacherId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Lesson::byTeacher($teacherId)->with(['teacher', 'institution']);

        $this->applyFilters($query, $filters);

        return $query->orderByDate()->paginate($perPage);
    }

    /**
     * Apply filters to the query
     */
    protected function applyFilters($query, array $filters): void
    {
        if (isset($filters['status']) && !empty($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        if (isset($filters['class_name']) && !empty($filters['class_name'])) {
            $query->byClass($filters['class_name']);
        }

        if (isset($filters['subject_name']) && !empty($filters['subject_name'])) {
            $query->bySubject($filters['subject_name']);
        }

        if (isset($filters['academic_year']) && !empty($filters['academic_year'])) {
            $query->byAcademicYear($filters['academic_year']);
        }

        if (isset($filters['date_from']) && isset($filters['date_to'])) {
            $query->byDateRange($filters['date_from'], $filters['date_to']);
        }
    }
}
