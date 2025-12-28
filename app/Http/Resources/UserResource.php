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
            'status' => $this->status,
            'avatar' => $this->avatar,
            'wilaya' => $this->wilaya,
            'municipality' => $this->municipality,
            'institution' => $this->when($this->relationLoaded('institution') && $this->institution, [
                'id' => $this->institution?->id,
                'name' => $this->institution?->name,
                'name_ar' => $this->institution?->name_ar,
                'type' => $this->institution?->type,
                // Include location info if needed, or rely on user's own location fields
                // 'wilaya' => $this->institution?->wilaya, 
                // 'municipality' => $this->institution?->municipality,
            ]),
            'user_institution_id' => $this->user_institution_id,
            'subjects' => $this->subjects,
            'levels' => $this->levels,
            'class' => $this->class,
            'linkedStudentId' => $this->linked_student_id,
            
            // Extended Profile
            'name_ar' => $this->name_ar,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth,
            'address' => $this->address,
            'phone' => $this->phone,

            // Teacher Specific
            'teacher_id' => $this->teacher_id,
            'years_of_experience' => $this->years_of_experience,
            'employment_status' => $this->employment_status,
            'weekly_teaching_load' => $this->weekly_teaching_load,
            'assigned_classes' => $this->when($this->isTeacher(), $this->assigned_classes ?? []),
            'groups' => $this->when($this->isTeacher(), $this->groups ?? []),
            
            // Admin/Staff specific fields
            'department' => $this->department,
            'position' => $this->position,
            'date_of_hiring' => $this->date_of_hiring,
            'work_phone' => $this->work_phone,
            'office_location' => $this->office_location,
            'notes' => $this->notes,

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'last_login_at' => $this->last_login_at?->toIso8601String(),
        ];
    }
}
