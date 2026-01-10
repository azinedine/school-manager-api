<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentReportResource extends JsonResource
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
            'report_number' => $this->report_number,
            'academic_year' => $this->academic_year,
            'report_date' => $this->report_date->format('Y-m-d'),
            'incident_description' => $this->incident_description,
            'sanctions' => $this->sanctions,
            'other_sanction' => $this->other_sanction,
            'status' => $this->status,
            'student' => [
                'id' => $this->student->id,
                'name' => $this->student->first_name . ' ' . $this->student->last_name,
                'class' => $this->meta['class_name'] ?? null, // Fallback to meta if available
            ],
            'teacher' => [
                'id' => $this->teacher->id,
                'name' => $this->teacher->name, // Assuming User has name
            ],
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
