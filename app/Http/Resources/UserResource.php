<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'avatar' => $this->avatar,
            'wilaya' => $this->wilaya,
            'municipality' => $this->municipality,
            'institution' => $this->when($this->relationLoaded('institution') && $this->institution, [
                'id' => $this->institution?->id,
                'name' => $this->institution?->name,
            ]),
            'subjects' => $this->subjects,
            'levels' => $this->levels,
            'class' => $this->class,
            'linkedStudentId' => $this->linked_student_id,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
