<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLessonPreparationRequest;
use App\Http\Resources\LessonPreparationResource;
use App\Models\LessonPreparation;
use App\Services\LessonPreparationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LessonPreparationController extends Controller
{
    protected $service;

    public function __construct(LessonPreparationService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', LessonPreparation::class);

        $teacherId = auth()->id();
        $filters = $request->only(['status', 'class', 'subject']);

        $preparations = $this->service->getPreparations($teacherId, $filters);

        return response()->json(
            LessonPreparationResource::collection($preparations)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLessonPreparationRequest $request): JsonResponse
    {
        $this->authorize('create', LessonPreparation::class);

        $teacherId = auth()->id();

        $preparation = $this->service->createPreparation(
            $teacherId,
            $request->validated()
        );

        return response()->json(
            new LessonPreparationResource($preparation),
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $preparation = $this->service->getPreparation($id);

        if (! $preparation) {
            return response()->json(
                ['error' => 'Lesson preparation not found'],
                404
            );
        }

        $this->authorize('view', $preparation);

        return response()->json(
            new LessonPreparationResource($preparation)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreLessonPreparationRequest $request, int $id): JsonResponse
    {
        $preparation = $this->service->getPreparation($id);

        if (! $preparation) {
            return response()->json(
                ['error' => 'Lesson preparation not found'],
                404
            );
        }

        $this->authorize('update', $preparation);

        $preparation = $this->service->updatePreparation($id, $request->validated());

        return response()->json(
            new LessonPreparationResource($preparation)
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $preparation = $this->service->getPreparation($id);

        if (! $preparation) {
            return response()->json(
                ['error' => 'Lesson preparation not found'],
                404
            );
        }

        $this->authorize('delete', $preparation);

        $this->service->deletePreparation($id);

        return response()->json(
            ['message' => 'Lesson preparation deleted successfully'],
            200
        );
    }

    /**
     * Get lesson preparation statistics for the dashboard.
     */
    public function statistics(): JsonResponse
    {
        $teacherId = auth()->id();
        $stats = $this->service->getStatistics($teacherId);

        return response()->json($stats);
    }
}
