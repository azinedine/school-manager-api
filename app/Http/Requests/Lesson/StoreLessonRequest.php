<?php

namespace App\Http\Requests\Lesson;

use Illuminate\Foundation\Http\FormRequest;

class StoreLessonRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization is handled by Policy
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     * 
     * NOTE: subject_name is NOT included in validation.
     * Subject is identity-bound to the teacher and resolved from auth user.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'content' => ['nullable', 'string', 'max:10000'],
            'lesson_date' => ['required', 'date', 'date_format:Y-m-d'],
            'academic_year' => [
                'required',
                'string',
                'max:20',
                'regex:/^\d{4}-\d{4}$/' // Format: 2024-2025
            ],
            'class_name' => ['required', 'string', 'min:1', 'max:100'],
            // NOTE: subject_name removed - resolved from authenticated teacher
            'status' => ['required', 'in:draft,published'],
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
            'title.max' => 'The lesson title must not exceed 255 characters.',
            'content.max' => 'The lesson content must not exceed 10000 characters.',
            'lesson_date.required' => 'Please select a date for the lesson.',
            'lesson_date.date_format' => 'The date format must be YYYY-MM-DD.',
            'academic_year.required' => 'The academic year is required.',
            'academic_year.regex' => 'The academic year format must be YYYY-YYYY (e.g., 2024-2025).',
            'class_name.required' => 'Please select a class.',
            'status.required' => 'Please select a status.',
            'status.in' => 'The selected status is invalid. Must be draft or published.',
        ];
    }

    /**
     * Get the validated data from the request.
     *
     * @return array<string, mixed>
     */
    public function validatedData(): array
    {
        return $this->only([
            'title',
            'content',
            'lesson_date',
            'academic_year',
            'class_name',
            // NOTE: subject_name removed - resolved from authenticated teacher
            'status',
        ]);
    }
}
