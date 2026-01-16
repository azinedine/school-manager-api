<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Lesson\StoreLessonRequest;
use App\Http\Requests\Lesson\UpdateLessonRequest;
use App\Http\Resources\LessonResource;
use App\Models\Lesson;
use App\Services\LessonService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LessonController extends Controller
{
    protected LessonService $service;

    public function __construct(LessonService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the lessons.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Lesson::class);

        $user = auth()->user();
        $filters = $request->only(['status', 'class_name', 'subject_name', 'academic_year', 'date_from', 'date_to']);
        $perPage = $request->input('per_page', 15);

        // Get paginated lessons for the current teacher
        $lessons = $this->service->getLessonsPaginated($user->id, $filters, $perPage);

        return LessonResource::collection($lessons);
    }

    /**
     * Store a newly created lesson in storage.
     */
    public function store(StoreLessonRequest $request): JsonResponse
    {
        $this->authorize('create', Lesson::class);

        $user = auth()->user();

        $lesson = $this->service->createLesson(
            $user->id,
            $user->institution_id,
            $request->validated()
        );

        return response()->json(
            new LessonResource($lesson),
            201
        );
    }

    /**
     * Display the specified lesson.
     */
    public function show(int $id): JsonResponse
    {
        $lesson = $this->service->getLesson($id);

        if (! $lesson) {
            return response()->json(
                ['message' => 'Lesson not found'],
                404
            );
        }

        $this->authorize('view', $lesson);

        return response()->json(
            new LessonResource($lesson)
        );
    }

    /**
     * Update the specified lesson in storage.
     */
    public function update(UpdateLessonRequest $request, int $id): JsonResponse
    {
        $lesson = $this->service->getLesson($id);

        if (! $lesson) {
            return response()->json(
                ['message' => 'Lesson not found'],
                404
            );
        }

        $this->authorize('update', $lesson);

        $updatedLesson = $this->service->updateLesson($id, $request->validated());

        return response()->json(
            new LessonResource($updatedLesson)
        );
    }

    /**
     * Remove the specified lesson from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $lesson = $this->service->getLesson($id);

        if (! $lesson) {
            return response()->json(
                ['message' => 'Lesson not found'],
                404
            );
        }

        $this->authorize('delete', $lesson);

        $this->service->deleteLesson($id);

        return response()->json(
            ['message' => 'Lesson deleted successfully']
        );
    }

    /**
     * Get lesson statistics for the dashboard.
     */
    public function statistics(): JsonResponse
    {
        $this->authorize('viewAny', Lesson::class);

        $teacherId = auth()->id();
        $stats = $this->service->getStatistics($teacherId);

        return response()->json($stats);
    }
}
