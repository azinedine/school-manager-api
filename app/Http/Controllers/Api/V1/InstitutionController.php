<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Institution\StoreInstitutionRequest;
use App\Http\Requests\Institution\UpdateInstitutionRequest;
use App\Http\Requests\Institution\GetInstitutionsRequest;
use App\Http\Resources\InstitutionResource;
use App\Models\Institution;
use App\Services\InstitutionService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class InstitutionController extends Controller
{
    use ApiResponse;

    public function __construct(
        private InstitutionService $institutionService
    ) {}

    /**
     * Display a listing of institutions.
     * 
     * GET /api/v1/institutions
     * 
     * Query params:
     *   - wilaya_id: Filter by wilaya
     *   - municipality_id: Filter by municipality
     *   - type: Filter by institution type
     *   - search: Search by name/address
     *   - is_active: Filter by active status
     *   - per_page: Items per page (default: 15)
     */
    public function index(GetInstitutionsRequest $request): JsonResponse
    {
        // Gate::authorize('viewAny', Institution::class); // Permissions handled by route/public access

        $filters = $request->only([
            'wilaya_id',
            'municipality_id',
            'type',
            'search',
            'is_active',
            'with_trashed',
        ]);

        $perPage = min($request->input('per_page', 15), 100);
        $institutions = $this->institutionService->list($filters, $perPage);

        return $this->successWithPagination(
            InstitutionResource::collection($institutions)->resource,
            'Institutions retrieved successfully'
        );
    }

    /**
     * Store a newly created institution.
     * 
     * POST /api/v1/institutions
     */
    public function store(StoreInstitutionRequest $request): JsonResponse
    {
        $institution = $this->institutionService->create($request->validated());

        return $this->created(
            new InstitutionResource($institution),
            'Institution created successfully'
        );
    }

    /**
     * Display the specified institution.
     * 
     * GET /api/v1/institutions/{institution}
     */
    public function show(Institution $institution): JsonResponse
    {
        // Gate::authorize('view', $institution); // Permissions handled by route/public access

        $institution->load(['wilaya', 'municipality']);

        return $this->success(
            new InstitutionResource($institution),
            'Institution retrieved successfully'
        );
    }

    /**
     * Update the specified institution.
     * 
     * PUT /api/v1/institutions/{institution}
     */
    public function update(UpdateInstitutionRequest $request, Institution $institution): JsonResponse
    {
        $institution = $this->institutionService->update($institution, $request->validated());

        return $this->success(
            new InstitutionResource($institution),
            'Institution updated successfully'
        );
    }

    /**
     * Remove the specified institution (soft delete).
     * 
     * DELETE /api/v1/institutions/{institution}
     */
    public function destroy(Institution $institution): JsonResponse
    {
        Gate::authorize('delete', $institution);

        $this->institutionService->delete($institution);

        return $this->success(null, 'Institution deleted successfully');
    }

    /**
     * Restore a soft-deleted institution.
     * 
     * POST /api/v1/institutions/{id}/restore
     */
    public function restore(int $id): JsonResponse
    {
        $institution = Institution::withTrashed()->findOrFail($id);
        Gate::authorize('restore', $institution);

        $this->institutionService->restore($id);

        return $this->success(null, 'Institution restored successfully');
    }

    /**
     * Get institutions by Wilaya and Municipality.
     * 
     * GET /api/v1/wilayas/{wilaya}/municipalities/{municipality}/institutions
     */
    public function getByLocation($wilayaId, $municipalityId): JsonResponse
    {
        // No Auth check needed as this is for public registration
        
        // We can use the service list method with strict filters
        $filters = [
            'wilaya_id' => $wilayaId,
            'municipality_id' => $municipalityId,
            'is_active' => true // Only active institutions for registration
        ];

        // Retrieve all matching institutions (not paginated, for dropdown)
        $institutions = $this->institutionService->all($filters);

        return $this->success(
            InstitutionResource::collection($institutions),
            'Institutions retrieved successfully'
        );
    }
}
