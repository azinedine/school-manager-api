<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\WilayaResource;
use App\Http\Resources\MunicipalityResource;
use App\Models\Wilaya;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class WilayaController extends Controller
{
    use ApiResponse;

    /**
     * List all wilayas.
     * 
     * GET /api/v1/wilayas
     */
    public function index(): JsonResponse
    {
        $wilayas = Wilaya::withCount(['municipalities', 'institutions'])
            ->orderBy('code')
            ->get();

        return $this->success(
            WilayaResource::collection($wilayas),
            'Wilayas retrieved successfully'
        );
    }

    /**
     * Get municipalities for a wilaya.
     * 
     * GET /api/v1/wilayas/{wilaya}/municipalities
     */
    public function municipalities(Wilaya $wilaya): JsonResponse
    {
        $municipalities = $wilaya->municipalities()
            ->withCount('institutions')
            ->orderBy('name')
            ->get();

        return $this->success(
            MunicipalityResource::collection($municipalities),
            'Municipalities retrieved successfully'
        );
    }
}
