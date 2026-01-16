<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
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
            'institution_id' => $this->institution_id,
            'teacher_id' => $this->teacher_id,

            // Resolved relationships
            'teacher_name' => $this->whenLoaded('teacher', fn () => $this->teacher->name),
            'institution_name' => $this->whenLoaded('institution', fn () => $this->institution->name),

            // Lesson details
            'title' => $this->title,
            'content' => $this->content,
            'lesson_date' => $this->lesson_date->toDateString(),
            'academic_year' => $this->academic_year,
            'class_name' => $this->class_name,
            'subject_name' => $this->subject_name,
            'status' => $this->status,

            // Timestamps in ISO8601 format
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
