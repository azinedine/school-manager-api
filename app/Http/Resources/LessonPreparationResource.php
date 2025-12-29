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
        // Helper to safely get property from object or array
        $get = fn($key, $default = null) => data_get($this->resource, $key, $default);

        return [
            'id' => $get('id'),
            'teacher_id' => $get('teacher_id'),
            'title' => $get('title'),
            'subject' => $get('subject'),
            'class' => $get('class'),
            'date' => $this->resource instanceof \App\Models\LessonPreparation ? $this->date->toDateString() : $get('date'),
            'duration_minutes' => $get('duration_minutes'),
            'learning_objectives' => $get('learning_objectives', []),
            'description' => $get('description'),
            'key_topics' => $get('key_topics', []),
            'teaching_methods' => $get('teaching_methods', []),
            'resources_needed' => $get('resources_needed', []),
            'assessment_methods' => $get('assessment_methods', []),
            'assessment_criteria' => $get('assessment_criteria'),
            'notes' => $get('notes'),
            'status' => $get('status'),
            'created_at' => $this->resource instanceof \App\Models\LessonPreparation 
                ? $this->created_at->toIso8601String() 
                : $get('created_at'),
            'updated_at' => $this->resource instanceof \App\Models\LessonPreparation 
                ? $this->updated_at->toIso8601String() 
                : $get('updated_at'),
        ];
    }
}
