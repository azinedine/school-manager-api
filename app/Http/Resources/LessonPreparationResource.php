<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonPreparationResource extends JsonResource
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
            'teacher_id' => $this->teacher_id,
            'title' => $this->title,
            'subject' => $this->subject,
            'class' => $this->class,
            'date' => $this->date->toDateString(),
            'duration_minutes' => $this->duration_minutes,
            'learning_objectives' => $this->learning_objectives ?? [],
            'description' => $this->description,
            'key_topics' => $this->key_topics ?? [],
            'teaching_methods' => $this->teaching_methods ?? [],
            'resources_needed' => $this->resources_needed ?? [],
            'assessment_methods' => $this->assessment_methods ?? [],
            'assessment_criteria' => $this->assessment_criteria,
            'notes' => $this->notes,
            'status' => $this->status,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
