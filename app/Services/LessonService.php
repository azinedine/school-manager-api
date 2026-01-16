<?php

namespace App\Services;

use App\Models\Lesson;
use App\Models\User;
use App\Repositories\Contracts\LessonRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LessonService
{
    protected LessonRepositoryInterface $repository;

    public function __construct(LessonRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get all lessons for a teacher with optional filters
     */
    public function getLessons(int $teacherId, array $filters = []): array
    {
        return $this->repository->getByTeacher($teacherId, $filters);
    }

    /**
     * Get all lessons for an institution with optional filters
     */
    public function getLessonsByInstitution(int $institutionId, array $filters = []): array
    {
        return $this->repository->getByInstitution($institutionId, $filters);
    }

    /**
     * Get paginated lessons for a teacher
     */
    public function getLessonsPaginated(int $teacherId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getFiltered($teacherId, $filters, $perPage);
    }

    /**
     * Get a single lesson by ID
     */
    public function getLesson(int $id): ?Lesson
    {
        return $this->repository->findById($id);
    }

    /**
     * Get a lesson by ID scoped to institution
     */
    public function getLessonInInstitution(int $id, int $institutionId): ?Lesson
    {
        return $this->repository->findByIdInInstitution($id, $institutionId);
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
     * Create a new lesson
     *
     * NOTE: subject_name is resolved from the teacher's profile,
     * not from the request payload.
     */
    public function createLesson(int $teacherId, int $institutionId, array $data): Lesson
    {
        // Set identity fields
        $data['teacher_id'] = $teacherId;
        $data['institution_id'] = $institutionId;

        // Resolve subject from teacher profile (identity-bound)
        $data['subject_name'] = $this->resolveSubjectFromTeacher($teacherId);

        // Set default status if not provided
        if (! isset($data['status'])) {
            $data['status'] = Lesson::STATUS_DRAFT;
        }

        return $this->repository->create($data);
    }

    /**
     * Update an existing lesson
     */
    public function updateLesson(int $id, array $data): Lesson
    {
        // Don't allow changing teacher, institution, or subject
        // Subject is identity-bound and cannot be modified
        unset($data['teacher_id'], $data['institution_id'], $data['subject_name']);

        return $this->repository->update($id, $data);
    }

    /**
     * Delete a lesson
     */
    public function deleteLesson(int $id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * Get lesson statistics for a teacher
     */
    public function getStatistics(int $teacherId): array
    {
        $allLessons = $this->getLessons($teacherId);

        $draftCount = 0;
        $publishedCount = 0;

        foreach ($allLessons as $lesson) {
            if ($lesson['status'] === Lesson::STATUS_DRAFT) {
                $draftCount++;
            } elseif ($lesson['status'] === Lesson::STATUS_PUBLISHED) {
                $publishedCount++;
            }
        }

        return [
            'total' => count($allLessons),
            'draft' => $draftCount,
            'published' => $publishedCount,
        ];
    }
}
