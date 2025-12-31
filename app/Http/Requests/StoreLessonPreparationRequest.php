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
        $rules = [
            'lesson_number' => ['required', 'integer', 'min:1'],
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
            'level' => ['required', 'string', 'min:1', 'max:50'],
            'date' => ['required', 'date'],
            'duration_minutes' => ['required', 'integer', 'min:15', 'max:480'],
            'learning_objectives' => ['required', 'array', 'min:1'],
            'learning_objectives.*' => ['string', 'min:1', 'max:500'],
            // Description removed
            'teaching_methods' => ['required', 'array', 'min:1'],
            'teaching_methods.*' => ['string', 'min:1', 'max:100'],
            'resources_needed' => ['nullable', 'array'],
            'resources_needed.*' => ['string', 'min:1', 'max:500'],
            'assessment_methods' => ['nullable', 'array'],
            'assessment_methods.*' => ['string', 'min:1', 'max:100'],
            // Assessment criteria removed
            'notes' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', 'in:draft,ready,delivered'],
            
            // Pedagogical Fields
            'domain' => ['required', 'string', 'min:3', 'max:255'],
            'learning_unit' => ['required', 'string', 'min:3', 'max:255'],
            'knowledge_resource' => ['required', 'string', 'min:3', 'max:255'],
            
            // Lesson Elements (Dynamic Array of Objects) - Optional in V2 if phases present, but kept required for generic validation
            // We can make it nullable or keep it as is. If UI sends empty array, it might fail min:1.
            // The schema sends it as optional? Frontend defaults to [{content: ''}] if empty.
            // Let's keep it required for now as legacy.
            'lesson_elements' => ['nullable', 'array'],
            'lesson_elements.*.content' => ['nullable', 'string'],
            
            // Pedagogical V2 Fields (Nullable/Optional)
            'targeted_knowledge' => ['nullable', 'array'],
            'targeted_knowledge.*' => ['string'],
            'used_materials' => ['nullable', 'array'],
            'used_materials.*' => ['string'],
            'references' => ['nullable', 'array'],
            'references.*' => ['string'],
            
            'phases' => ['nullable', 'array'],
            'phases.*.type' => ['required_with:phases', 'string', 'in:departure,presentation,consolidation'],
            'phases.*.content' => ['required_with:phases', 'string'],
            'phases.*.duration_minutes' => ['required_with:phases', 'numeric', 'min:1'],

            'activities' => ['nullable', 'array'],
            'activities.*.content' => ['required_with:activities', 'string'],
            
            // Evaluation (Discriminator)
            'evaluation_type' => ['required', 'in:assessment,homework'],
            'evaluation_content' => ['required', 'string', 'min:3'],
        ];

        if ($this->isMethod('patch')) {
            return array_map(function ($rule) {
                // Prepend 'sometimes' to the rule array or string
                if (is_array($rule)) {
                    array_unshift($rule, 'sometimes');
                    return $rule;
                }
                return 'sometimes|' . $rule;
            }, $rules);
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'lesson_number.required' => 'The lesson number is required.',
            'lesson_number.min' => 'The lesson number must be at least 1 character.',
            'level.required' => 'Please select a level.',
            'date.required' => 'Please select a date.',
            'duration_minutes.required' => 'Please enter the lesson duration.',
            'duration_minutes.min' => 'Duration must be at least 15 minutes.',
            'learning_objectives.required' => 'Add at least one learning objective.',
            'learning_objectives.min' => 'Add at least one learning objective.',
            'teaching_methods.required' => 'Select at least one teaching method.',
            'status.in' => 'The selected status is invalid.',
        ];
    }
}
