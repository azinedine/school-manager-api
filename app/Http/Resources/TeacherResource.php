<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeacherResource extends JsonResource
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
            'name_ar' => $this->name_ar,
            'email' => $this->email,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth,

            // Resolved Relationships
            'institution' => [
                'id' => $this->institution?->id,
                'name' => $this->institution?->name,
                'name_ar' => $this->institution?->name_ar,
            ],
            'wilaya' => [
                'id' => $this->wilaya?->id,
                'name' => $this->wilaya?->name,
                'name_ar' => $this->wilaya?->name_ar,
            ],
            'municipality' => [
                'id' => $this->municipality?->id,
                'name' => $this->municipality?->name,
                'name_ar' => $this->municipality?->name_ar,
            ],

            // Teacher Specific
            'teacher_id' => $this->teacher_id,
            'years_of_experience' => $this->years_of_experience,
            'employment_status' => $this->employment_status,
            'weekly_teaching_load' => $this->weekly_teaching_load,
            'subjects' => $this->subjects,
            'levels' => $this->levels,

            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
