<?php

namespace App\Services;

use App\Models\Institution;
use App\Models\Municipality;
use App\Repositories\Contracts\InstitutionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class InstitutionService
{
    public function __construct(
        private InstitutionRepositoryInterface $repository
    ) {}

    /**
     * Get paginated list of institutions with filters.
     */
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, $perPage);
    }

    /**
     * Get all institutions (for dropdowns, etc.)
     */
    public function all(array $filters = []): Collection
    {
        return $this->repository->all($filters);
    }

    /**
     * Find institution by ID.
     */
    public function find(int $id): ?Institution
    {
        return $this->repository->find($id);
    }

    /**
     * Find institution by ID or fail.
     */
    public function findOrFail(int $id): Institution
    {
        return $this->repository->findOrFail($id);
    }

    /**
     * Create a new institution.
     *
     * @throws ValidationException
     */
    public function create(array $data): Institution
    {
        // Business rule: Validate municipality belongs to wilaya
        $this->validateMunicipalityBelongsToWilaya($data);

        return $this->repository->create($data);
    }

    /**
     * Update an institution.
     *
     * @throws ValidationException
     */
    public function update(Institution $institution, array $data): Institution
    {
        // Business rule: Validate municipality belongs to wilaya if changing
        if (isset($data['municipality_id']) || isset($data['wilaya_id'])) {
            $this->validateMunicipalityBelongsToWilaya(
                array_merge($institution->toArray(), $data)
            );
        }

        return $this->repository->update($institution, $data);
    }

    /**
     * Delete an institution (soft delete).
     */
    public function delete(Institution $institution): bool
    {
        return $this->repository->delete($institution);
    }

    /**
     * Restore a soft-deleted institution.
     */
    public function restore(int $id): bool
    {
        return $this->repository->restore($id);
    }

    /**
     * Permanently delete an institution.
     */
    public function forceDelete(Institution $institution): bool
    {
        return $this->repository->forceDelete($institution);
    }

    /**
     * Get institutions by wilaya.
     */
    public function getByWilaya(int $wilayaId): Collection
    {
        return $this->repository->getByWilaya($wilayaId);
    }

    /**
     * Get institutions by municipality.
     */
    public function getByMunicipality(int $municipalityId): Collection
    {
        return $this->repository->getByMunicipality($municipalityId);
    }

    /**
     * Business rule: Validate that municipality belongs to the specified wilaya.
     *
     * @throws ValidationException
     */
    private function validateMunicipalityBelongsToWilaya(array $data): void
    {
        if (empty($data['municipality_id']) || empty($data['wilaya_id'])) {
            return;
        }

        $municipality = Municipality::find($data['municipality_id']);

        if (! $municipality || $municipality->wilaya_id != $data['wilaya_id']) {
            throw ValidationException::withMessages([
                'municipality_id' => ['The selected municipality does not belong to the selected wilaya.'],
            ]);
        }
    }
}
