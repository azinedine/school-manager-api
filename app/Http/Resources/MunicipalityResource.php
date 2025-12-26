<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MunicipalityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'wilaya_id' => $this->wilaya_id,
            'name' => $this->name,
            'name_ar' => $this->name_ar,
            'wilaya' => new WilayaResource($this->whenLoaded('wilaya')),
            'institutions_count' => $this->whenCounted('institutions'),
        ];
    }
}
