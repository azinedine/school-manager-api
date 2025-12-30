<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLessonPreparationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isTeacher();
    }

    /**
     * Get the validation rules that apply to the request.
     * 
     * NOTE: subject is removed from validation.
     * Subject is identity-bound to the teacher and resolved from auth user.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'subject' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $teacherSubjects = auth()->user()->subjects ?? [];
                    // Ensure subjects is an array
                    if (!is_array($teacherSubjects)) {
                        $teacherSubjects = [];
                    }
                    
                    if (!in_array($value, $teacherSubjects)) {
                        $fail("The selected subject is not in your assigned subjects list.");
                    }
                },
            ],
            'class' => ['required', 'string', 'min:1', 'max:50'],
            'date' => ['required', 'date', 'date_format:Y-m-d'],
            'duration_minutes' => ['required', 'integer', 'min:15', 'max:480'],
            'learning_objectives' => ['required', 'array', 'min:1'],
            'learning_objectives.*' => ['string', 'min:1', 'max:500'],
            'description' => ['nullable', 'string', 'max:2000'],
            'key_topics' => ['required', 'array', 'min:1'],
            'key_topics.*' => ['string', 'min:1', 'max:500'],
            'teaching_methods' => ['required', 'array', 'min:1'],
            'teaching_methods.*' => ['string', 'min:1', 'max:100'],
            'resources_needed' => ['nullable', 'array'],
            'resources_needed.*' => ['string', 'min:1', 'max:500'],
            'assessment_methods' => ['nullable', 'array'],
            'assessment_methods.*' => ['string', 'min:1', 'max:100'],
            'assessment_criteria' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', 'in:draft,ready,delivered'],
            
            // Pedagogical Fields
            'domain' => ['required', 'string', 'min:3', 'max:255'],
            'learning_unit' => ['required', 'string', 'min:3', 'max:255'],
            'knowledge_resource' => ['required', 'string', 'min:3', 'max:255'],
            
            // Lesson Elements (Dynamic Array of Objects)
            'lesson_elements' => ['required', 'array', 'min:1'],
            'lesson_elements.*.content' => ['required', 'string', 'min:1'],
            
            // Evaluation (Discriminator)
            'evaluation_type' => ['required', 'in:assessment,homework'],
            'evaluation_content' => ['required', 'string', 'min:3'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'The lesson title is required.',
            'title.min' => 'The lesson title must be at least 3 characters.',
            'class.required' => 'Please select a class.',
            'date.required' => 'Please select a date.',
            'date.date_format' => 'The date format must be YYYY-MM-DD.',
            'duration_minutes.required' => 'Please enter the lesson duration.',
            'duration_minutes.min' => 'Duration must be at least 15 minutes.',
            'learning_objectives.required' => 'Add at least one learning objective.',
            'learning_objectives.min' => 'Add at least one learning objective.',
            'key_topics.required' => 'Add at least one key topic.',
            'teaching_methods.required' => 'Select at least one teaching method.',
            'status.in' => 'The selected status is invalid.',
        ];
    }
}
