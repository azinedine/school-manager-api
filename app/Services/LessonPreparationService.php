<?php

namespace App\Services;

use App\Models\LessonPreparation;
use App\Models\User;
use App\Repositories\Contracts\LessonPreparationRepositoryInterface;
use Illuminate\Pagination\Paginator;

class LessonPreparationService
{
    protected $repository;

    public function __construct(LessonPreparationRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get all lesson preparations for a teacher with filters
     */
    public function getPreparations(int $teacherId, array $filters = []): array
    {
        return $this->repository->getByTeacher($teacherId, $filters);
    }

    /**
     * Get filtered and paginated lesson preparations
     */
    public function getPreparationsPaginated(int $teacherId, array $filters = [], int $perPage = 15): Paginator
    {
        return $this->repository->getFiltered($teacherId, $filters, $perPage);
    }

    /**
     * Get a single lesson preparation
     */
    public function getPreparation(int $id): ?LessonPreparation
    {
        return $this->repository->findById($id);
    }

    /**
     * Resolve subject name from teacher profile
     *
     * Subject is identity-bound to the teacher, not lesson-bound.
     * Takes the first subject from the teacher's subjects array.
     */
    protected function resolveSubjectFromTeacher(int $teacherId): string
    {
        $teacher = User::find($teacherId);

        if (! $teacher) {
            return 'Unknown';
        }

        $subjects = $teacher->subjects;

        // If subjects is an array and has values, use the first one
        if (is_array($subjects) && count($subjects) > 0) {
            return $subjects[0];
        }

        // Fallback to empty or default
        return 'General';
    }

    /**
     * Create a new lesson preparation
     */
    /**
     * Create a new lesson preparation
     */
    public function createPreparation(int $teacherId, array $data): LessonPreparation
    {
        // Ensure the teacher_id is set
        $data['teacher_id'] = $teacherId;

        // Subject is now passed in $data and validated by the Request
        // but if missing (fallback), try to resolve it
        if (! isset($data['subject']) || empty($data['subject'])) {
            $data['subject'] = $this->resolveSubjectFromTeacher($teacherId);
        }

        // Validate that arrays are properly formatted
        $data = $this->formatArrayFields($data);

        return $this->repository->create($data);
    }

    /**
     * Update an existing lesson preparation
     */
    public function updatePreparation(int $id, array $data): LessonPreparation
    {
        // Don't allow changing teacher
        unset($data['teacher_id']);

        // Subject CAN be updated now if the teacher wants to correct it

        // Validate that arrays are properly formatted
        $data = $this->formatArrayFields($data);

        return $this->repository->update($id, $data);
    }

    /**
     * Delete a lesson preparation
     */
    public function deletePreparation(int $id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * Get preparations by status
     */
    public function getByStatus(int $teacherId, string $status): array
    {
        return $this->repository->getByStatus($teacherId, $status);
    }

    /**
     * Get preparations by class
     */
    public function getByClass(int $teacherId, string $class): array
    {
        return $this->repository->getByClass($teacherId, $class);
    }

    /**
     * Get preparations by subject
     */
    public function getBySubject(int $teacherId, string $subject): array
    {
        return $this->repository->getBySubject($teacherId, $subject);
    }

    /**
     * Get statistics for dashboard
     */
    public function getStatistics(int $teacherId): array
    {
        $allPreps = $this->getPreparations($teacherId);

        return [
            'total' => count($allPreps),
            'draft' => count($this->getByStatus($teacherId, 'draft')),
            'ready' => count($this->getByStatus($teacherId, 'ready')),
            'delivered' => count($this->getByStatus($teacherId, 'delivered')),
        ];
    }

    /**
     * Format array fields to ensure they're properly stored
     */
    private function formatArrayFields(array $data): array
    {
        $arrayFields = [
            'learning_objectives',
            'teaching_methods',
            'resources_needed',
            'assessment_methods',
        ];

        foreach ($arrayFields as $field) {
            if (isset($data[$field]) && is_array($data[$field])) {
                // Filter out empty strings
                $data[$field] = array_filter($data[$field], fn ($item) => ! empty(trim($item)));
                // Reindex array
                $data[$field] = array_values($data[$field]);
            }
        }

        return $data;
    }
}
