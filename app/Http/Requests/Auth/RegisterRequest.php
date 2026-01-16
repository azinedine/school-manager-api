<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:super_admin,admin,teacher,student,parent'],
            'wilaya' => ['required', 'string'],
            'municipality' => ['required', 'string'],
            'institution_id' => ['required', 'integer', 'exists:institutions,id'],

            // Teacher fields (required when role=teacher)
            'name_ar' => ['required_if:role,teacher', 'nullable', 'string'],
            'gender' => ['required_if:role,teacher', 'nullable', 'in:male,female'],
            'date_of_birth' => ['required_if:role,teacher', 'nullable', 'date'],
            'phone' => ['required_if:role,teacher', 'nullable', 'string'],
            'years_of_experience' => ['required_if:role,teacher', 'nullable', 'integer', 'min:0'],
            'subjects' => ['required_if:role,teacher', 'nullable', 'array', 'min:1'],
            'levels' => ['required_if:role,teacher', 'nullable', 'array', 'min:1'],

            // Student fields
            'class' => ['required_if:role,student', 'nullable', 'string'],

            // Parent fields
            'linked_student_id' => ['required_if:role,parent', 'nullable', 'string'],

            // Admin fields
            'department' => ['required_if:role,admin', 'nullable', 'string'],
            'position' => ['required_if:role,admin', 'nullable', 'string'],
            'date_of_hiring' => ['required_if:role,admin', 'nullable', 'date'],
            'work_phone' => ['required_if:role,admin', 'nullable', 'string'],
            'office_location' => ['required_if:role,admin', 'nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
