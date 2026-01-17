<?php

namespace App\Http\Resources;

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
        // Load relationships - note: wilaya/municipality columns store IDs, relationships have same name
        // Use getRelationValue to get the loaded relationship model
        $wilayaModel = $this->getRelationValue('wilaya') ?? $this->wilaya()->first();
        $municipalityModel = $this->getRelationValue('municipality') ?? $this->municipality()->first();
        $institutionModel = $this->getRelationValue('institution') ?? $this->institution;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'status' => $this->status ?? 'active',
            'avatar' => $this->avatar,
            'wilaya' => $wilayaModel ? [
                'id' => $wilayaModel->id,
                'name' => $wilayaModel->name,
                'name_ar' => $wilayaModel->name_ar,
            ] : null,
            'municipality' => $municipalityModel ? [
                'id' => $municipalityModel->id,
                'name' => $municipalityModel->name,
                'name_ar' => $municipalityModel->name_ar,
            ] : null,
            'institution_id' => $this->institution_id,
            'institution' => $institutionModel ? [
                'id' => $institutionModel->id,
                'name' => $institutionModel->name,
                'name_ar' => $institutionModel->name_ar,
                'type' => $institutionModel->type,
            ] : null,
            'user_institution_id' => $this->user_institution_id,
            'subjects' => $this->subjects ?? [],
            'levels' => $this->levels ?? [],
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
