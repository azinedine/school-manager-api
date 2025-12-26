<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InstitutionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'wilaya_id' => $this->wilaya_id,
            'municipality_id' => $this->municipality_id,
            'name' => $this->name,
            'name_ar' => $this->name_ar,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'type' => $this->type,
            'is_active' => $this->is_active,
            'wilaya' => new WilayaResource($this->whenLoaded('wilaya')),
            'municipality' => new MunicipalityResource($this->whenLoaded('municipality')),
            'users_count' => $this->whenCounted('users'),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'deleted_at' => $this->deleted_at?->toISOString(),
        ];
    }
}
