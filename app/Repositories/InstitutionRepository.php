<?php

namespace App\Repositories;

use App\Models\Institution;
use App\Repositories\Contracts\InstitutionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class InstitutionRepository implements InstitutionRepositoryInterface
{
    private const CACHE_TTL = 3600; // 1 hour

    private const CACHE_PREFIX = 'institutions:';

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Institution::query()->with(['wilaya', 'municipality']);

        $this->applyFilters($query, $filters);

        return $query->orderBy('name')->paginate($perPage);
    }

    public function all(array $filters = []): Collection
    {
        $cacheKey = self::CACHE_PREFIX.'all:'.md5(json_encode($filters));

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($filters) {
            $query = Institution::query()->with(['wilaya', 'municipality']);
            $this->applyFilters($query, $filters);

            return $query->orderBy('name')->get();
        });
    }

    public function find(int $id): ?Institution
    {
        return Cache::remember(
            self::CACHE_PREFIX.$id,
            self::CACHE_TTL,
            fn () => Institution::with(['wilaya', 'municipality'])->find($id)
        );
    }

    public function findOrFail(int $id): Institution
    {
        return Institution::with(['wilaya', 'municipality'])->findOrFail($id);
    }

    public function create(array $data): Institution
    {
        $institution = Institution::create($data);
        $this->clearCache();

        return $institution->load(['wilaya', 'municipality']);
    }

    public function update(Institution $institution, array $data): Institution
    {
        $institution->update($data);
        $this->clearCache();
        $this->clearInstanceCache($institution->id);

        return $institution->fresh(['wilaya', 'municipality']);
    }

    public function delete(Institution $institution): bool
    {
        $result = $institution->delete();
        $this->clearCache();
        $this->clearInstanceCache($institution->id);

        return $result;
    }

    public function restore(int $id): bool
    {
        $institution = Institution::withTrashed()->findOrFail($id);
        $result = $institution->restore();
        $this->clearCache();

        return $result;
    }

    public function forceDelete(Institution $institution): bool
    {
        $result = $institution->forceDelete();
        $this->clearCache();
        $this->clearInstanceCache($institution->id);

        return $result;
    }

    public function getByWilaya(int $wilayaId): Collection
    {
        return Cache::remember(
            self::CACHE_PREFIX.'wilaya:'.$wilayaId,
            self::CACHE_TTL,
            fn () => Institution::with(['municipality'])
                ->inWilaya($wilayaId)
                ->active()
                ->orderBy('name')
                ->get()
        );
    }

    public function getByMunicipality(int $municipalityId): Collection
    {
        return Cache::remember(
            self::CACHE_PREFIX.'municipality:'.$municipalityId,
            self::CACHE_TTL,
            fn () => Institution::inMunicipality($municipalityId)
                ->active()
                ->orderBy('name')
                ->get()
        );
    }

    /**
     * Apply filters to the query.
     */
    private function applyFilters($query, array $filters): void
    {
        if (! empty($filters['wilaya_id'])) {
            $query->inWilaya($filters['wilaya_id']);
        }

        if (! empty($filters['municipality_id'])) {
            $query->inMunicipality($filters['municipality_id']);
        }

        if (! empty($filters['type'])) {
            $query->ofType($filters['type']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('name_ar', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        if (isset($filters['with_trashed']) && $filters['with_trashed']) {
            $query->withTrashed();
        }
    }

    /**
     * Clear all institution list caches.
     */
    private function clearCache(): void
    {
        Cache::forget(self::CACHE_PREFIX.'all:'.md5(json_encode([])));
        // In production, use cache tags for more efficient invalidation
    }

    /**
     * Clear cache for a specific institution.
     */
    private function clearInstanceCache(int $id): void
    {
        Cache::forget(self::CACHE_PREFIX.$id);
    }
}
