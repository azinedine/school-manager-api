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
            'lesson_number' => $get('lesson_number'),
            'subject' => $get('subject'),
            'level' => $get('level'),
            'date' => $this->resource instanceof \App\Models\LessonPreparation ? $this->date->toDateString() : $get('date'),
            'duration_minutes' => $get('duration_minutes'),
            'learning_objectives' => $get('learning_objectives', []),
            'teaching_methods' => $get('teaching_methods', []),
            'resources_needed' => $get('resources_needed', []),
            'assessment_methods' => $get('assessment_methods', []),
            'notes' => $get('notes'),
            'domain' => $get('domain'),
            'learning_unit' => $get('learning_unit'),
            'knowledge_resource' => $get('knowledge_resource'),
            'lesson_elements' => $get('lesson_elements', []),
            
            // Pedagogical V2 Fields
            'targeted_knowledge' => $get('targeted_knowledge', []),
            'used_materials' => $get('used_materials', []),
            'references' => $get('references', []),
            'phases' => $get('phases', []),
            'activities' => $get('activities', []),

            'evaluation_type' => $get('evaluation_type'),
            'evaluation_content' => $get('evaluation_content'),
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
