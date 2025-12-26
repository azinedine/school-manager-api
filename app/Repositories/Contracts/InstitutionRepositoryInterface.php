<?php

namespace App\Repositories\Contracts;

use App\Models\Institution;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface InstitutionRepositoryInterface
{
    /**
     * Get all institutions with pagination.
     */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Get all institutions without pagination.
     */
    public function all(array $filters = []): Collection;

    /**
     * Find institution by ID.
     */
    public function find(int $id): ?Institution;

    /**
     * Find institution by ID or fail.
     */
    public function findOrFail(int $id): Institution;

    /**
     * Create a new institution.
     */
    public function create(array $data): Institution;

    /**
     * Update an institution.
     */
    public function update(Institution $institution, array $data): Institution;

    /**
     * Delete an institution (soft delete).
     */
    public function delete(Institution $institution): bool;

    /**
     * Restore a soft-deleted institution.
     */
    public function restore(int $id): bool;

    /**
     * Force delete an institution.
     */
    public function forceDelete(Institution $institution): bool;

    /**
     * Get institutions by wilaya.
     */
    public function getByWilaya(int $wilayaId): Collection;

    /**
     * Get institutions by municipality.
     */
    public function getByMunicipality(int $municipalityId): Collection;
}
