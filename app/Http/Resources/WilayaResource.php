<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WilayaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'name_ar' => $this->name_ar,
            'municipalities_count' => $this->whenCounted('municipalities'),
            'institutions_count' => $this->whenCounted('institutions'),
        ];
    }
}
