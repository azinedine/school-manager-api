<?php

namespace App\Http\Requests\Lesson;

class UpdateLessonRequest extends StoreLessonRequest
{
    /**
     * Get the validation rules that apply to the request.
     * 
     * For updates, all fields are optional but must pass validation if provided.
     * NOTE: subject_name is NOT included - it's identity-bound and cannot be updated.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'min:3', 'max:255'],
            'content' => ['nullable', 'string', 'max:10000'],
            'lesson_date' => ['sometimes', 'required', 'date', 'date_format:Y-m-d'],
            'academic_year' => [
                'sometimes',
                'required',
                'string',
                'max:20',
                'regex:/^\d{4}-\d{4}$/'
            ],
            'class_name' => ['sometimes', 'required', 'string', 'min:1', 'max:100'],
            // NOTE: subject_name removed - identity-bound, cannot be updated
            'status' => ['sometimes', 'required', 'in:draft,published'],
        ];
    }
}
